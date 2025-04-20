<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Product;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Exports\SalesExport;
use Maatwebsite\Excel\Facades\Excel;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Sale::with('user')->latest();

        if ($request->has('search') && $request->search !== null) {
            $search = strtolower($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereRaw('LOWER(invoice_number) LIKE ?', ['%'.$search.'%'])
                  ->orWhereRaw('LOWER(customer_name) LIKE ?', ['%'.$search.'%']);
            });
        }

        $sales = $query->paginate(10)->appends($request->only('search'));

        return view('sales.index', compact('sales'));
    }

    public function create()
    {
        $products = Product::where('quantity', '>', 0)
            ->orderByRaw('LOWER(name) ASC')
            ->get();

        $members = Member::select('id', 'name')->get(); // Optimasi: hanya ambil kolom yang diperlukan

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

        $members = Member::select('id', 'name')->get();

        return view('sales.confirmation', compact('products', 'totalAmount', 'members', 'filteredQuantities'));
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'product_data' => 'required',
            'total_pay' => 'required|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'member_id' => 'nullable|exists:members,id',
            'total_point' => 'nullable|numeric|min:0',
        ]);

        $productData = json_decode($request->input('product_data'), true);

        if (!$productData || !is_array($productData)) {
            Log::error('Invalid product data in sale creation', [
                'product_data' => $request->input('product_data')
            ]);
            return redirect()->back()->with('error', 'Invalid product data.');
        }

        foreach ($productData as $product) {
            $dbProduct = Product::find($product['id']);
            if (!$dbProduct || $dbProduct->quantity < $product['quantity']) {
                Log::warning('Product unavailable or insufficient stock', [
                    'product_id' => $product['id'],
                    'requested_quantity' => $product['quantity'],
                    'available_quantity' => $dbProduct->quantity ?? 0
                ]);
                return redirect()->back()->with('error', 'One or more products are no longer available or have insufficient stock.');
            }
        }

        $totalPay = (float) $request->input('total_pay');
        $totalAmount = (float) $request->input('total_amount');
        $invoiceNumber = 'INV-' . strtoupper(Str::random(8));

        $memberName = $invoiceNumber;
        $memberId = null;
        $member = null;

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
            $totalPoint = (float) $request->total_point;
            $totalAmount = $totalAmount - $totalPoint;
            if ($member) {
                Member::where('id', $memberId)->decrement('points', $totalPoint);
                Log::info('Points deducted from member', [
                    'member_id' => $memberId,
                    'points_deducted' => $totalPoint
                ]);
            }
        } else {
            $addPoint = floor($totalAmount / 750);
            if ($member && $addPoint > 0) {
                Member::where('id', $memberId)->increment('points', $addPoint);
                Log::info('Points added to member', [
                    'member_id' => $memberId,
                    'points_added' => $addPoint
                ]);
            }
        }

        $sale = Sale::create([
            'id' => Str::uuid(),
            'invoice_number' => $invoiceNumber,
            'customer_name' => $memberName,
            'user_id' => Auth::user()->id,
            'member_id' => $memberId,
            'product_data' => json_encode($productData),
            'total_amount' => $totalAmount,
            'payment_amount' => $totalPay,
            'change_amount' => max(0, $totalPay - $totalAmount), // Pastikan change_amount tidak negatif
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
                Log::info('Product deleted due to zero quantity', [
                    'product_id' => $product['id']
                ]);
            }
        }

        if ($request->use_point == 1) {
            $totalAmount = $totalAmount + $request->total_point;
            $discount = $request->total_point;
        } else {
            $discount = 0;
        }

        Log::info('Sale created successfully', [
            'sale_id' => $sale->id,
            'invoice_number' => $invoiceNumber,
            'user_id' => Auth::user()->id
        ]);

        return view('sales.invoice', compact('invoiceNumber', 'totalAmount', 'totalPay', 'memberName', 'memberId', 'productData', 'discount'));
    }

    public function showInvoice($id)
    {
        $sale = Sale::with('member')->where('id', $id)->firstOrFail();

        $productData = $sale->product_data; // product_data sudah di-cast sebagai array di model Sale

        if (!$productData || !is_array($productData)) {
            Log::warning('Invalid product data in sale invoice', [
                'sale_id' => $id,
                'product_data' => $sale->product_data
            ]);
            $productData = [];
        }

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

    public function export(Request $request)
    {
        try {
            Log::info('Exporting sales data', [
                'user' => $request->user()->email,
                'search' => $request->query('search')
            ]);
            $search = $request->query('search');
            return Excel::download(new SalesExport($search), 'sales_' . now()->format('Y-m-d') . '.xlsx');
        } catch (\Exception $e) {
            Log::error('Failed to export sales', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user' => $request->user()->email
            ]);
            return redirect()->back()->with('error', 'Failed to export sales data. Please try again or contact the administrator.');
        }
    }
}