<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Exports\SalesExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        if($request->has('search') && $request->search !== null) {
            $search = strtolower($request->search);
            $sales = Sale::whereRaw('LOWER(invoice_number) LIKE ?', ['%'.$search.'%'])
                ->paginate(10)
                ->appends($request->only('search'));
        } else {
            $sales = Sale::latest()->paginate(10);
        }

        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $products = Product::where('quantity', '>', 0)
            ->orderByRaw('LOWER(name) ASC')
            ->get();

        $members = Member::all();
        return view('sales.create', compact('products', 'members'));
    }

    public function confirmationStore(Request $request)
    {
        $filteredQuantities = [];

        foreach ($request->input('quantities', []) as $key => $value) {
            if ($value > 0) {
                $product = Product::find($key);
                if ($product && $product->quantity >= $value) {
                    $filteredQuantities[$key] = $value;
                }
            }
        }

        if (empty($filteredQuantities)) {
            return redirect()->back()->with('error', 'No valid products selected or insufficient stock.');
        }

        $products = Product::whereIn('id', array_keys($filteredQuantities))->get();
        $totalAmount = $products->sum(function ($product) use ($filteredQuantities) {
            return $product->price * $filteredQuantities[$product->id];
        });

        $members = Member::all();

        return view('sales.confirmation', compact('products', 'totalAmount', 'members', 'filteredQuantities'));
    }

    public function store(Request $request)
    {
        $productData = json_decode($request->input('product_data'), true);

        foreach ($productData as $product) {
            $dbProduct = Product::find($product['id']);
            if (!$dbProduct || $dbProduct->quantity < $product['quantity']) {
                return redirect()->back()->with('error', 'One or more products are no longer available or have insufficient stock.');
            }
        }

        $totalPay = $request->input('total_pay');
        $totalAmount = $request->input('total_amount');
        $invoiceNumber = 'INV-' . strtoupper(Str::random(8));

        $memberName = $invoiceNumber;
        $memberId = null;

        if (!empty($request->member_id)) {
            $member = Member::find($request->member_id);
            if ($member) {
                $memberId = $member->id;
                $memberName = $member->name;
            }
        }

        if ($request->is_member == 'yes') {
            $products = $productData;
            return view('sales.member', compact('member', 'products', 'totalAmount', 'totalPay'));
        }

        if ($request->use_point == 1) {
            $totalAmount = $totalAmount - $request->total_point;
            Member::where('id', $memberId)->decrement('points', $request->total_point);
        } else {
            $addPoint = $totalAmount / 750;
            Member::where('id', $memberId)->increment('points', $addPoint);
        }

        Sale::create([
            'id' => Str::uuid(),
            'invoice_number' => $invoiceNumber,
            'customer_name' => $memberName,
            'user_id' => Auth::user()->id,
            'member_id' => $memberId,
            'product_data' => json_encode($productData),
            'total_amount' => $totalAmount,
            'payment_amount' => $totalPay,
            'change_amount' => $totalPay - $totalAmount,
            'notes' => '-',
        ]);

        foreach ($productData as $product) {
            $dbProduct = Product::find($product['id']);
            $dbProduct->decrement('quantity', $product['quantity']);

            if ($dbProduct->quantity <= 0) {
                if ($dbProduct->image && Storage::disk('public')->exists($dbProduct->image)) {
                    Storage::disk('public')->delete($dbProduct->image);
                }
                $dbProduct->delete();
            }
        }

        if ($request->use_point == 1) {
            $totalAmount = $totalAmount + $request->total_point;
            $discount = $request->total_point;
        } else {
            $discount = 0;
        }

        return view('sales.invoice', compact('invoiceNumber', 'totalAmount', 'totalPay', 'memberName', 'memberId', 'productData', 'discount'));
    }

    public function showInvoice($id)
    {
        $sale = Sale::where('id', $id)->firstOrFail();

        $productData = json_decode($sale->product_data, true);

        $totalProductPrice = array_reduce($productData, function ($carry, $item) {
            return $carry + ($item['price'] * $item['quantity']);
        }, 0);

        $discount = $totalProductPrice - $sale->total_amount;

        return view('sales.invoice-detail', [
            'invoiceNumber' => $sale->invoice_number,
            'memberName'    => $sale->customer_name,
            'memberId'      => $sale->member_id,
            'productData'   => $productData,
            'totalAmount'   => $sale->total_amount,
            'totalPay'      => $sale->payment_amount,
            'changeAmount'  => $sale->change_amount,
            'discount'      => $discount,
            'createdAt'     => $sale->created_at
        ]);
    }

    /**
     * Export sales data to Excel
     *
     * @return BinaryFileResponse
     */
    public function export(): BinaryFileResponse
    {
        // Authorization check
        if (!in_array(Auth::user()->role, ['admin', 'manager', 'superadmin'])) {
            abort(403, 'Unauthorized action.');
        }

        $fileName = 'sales_report_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new SalesExport(), $fileName);
    }
}
