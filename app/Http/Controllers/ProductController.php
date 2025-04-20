<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Always exclude products with quantity <= 0
        $query = Product::where('quantity', '>', 0);

        if ($request->has('search') && $request->search !== null) {
            $search = strtolower($request->search);
            $products = $query->whereRaw('LOWER(name) LIKE ?', ['%'.$search.'%'])
                ->orderByRaw('LOWER(name) ASC')
                ->paginate(10)
                ->appends($request->only('search'));
        } else {
            $products = $query->orderByRaw('LOWER(name) ASC')->paginate(10);
        }

        return view('products.index', compact('products'));
    }

    public function create()
    {
        return view('products.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric',
        ]);

        // Don't create product if quantity is 0
        if ($request->quantity <= 0) {
            return back()->with('error', 'Product quantity must be greater than 0.');
        }

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('images/products', 'public');
        }

        Product::create([
            'name' => $request->name,
            'image' => $imagePath ?? null,
            'quantity' => $request->quantity,
            'price' => $request->price,
        ]);

        return redirect()->route('products.index')->with('message', 'Product created successfully.');
    }

    public function show(Product $product)
    {
        // Don't show out-of-stock products
        if ($product->quantity <= 0) {
            abort(404);
        }
        return view('products.show', compact('product'));
    }

    public function edit(Product $product)
    {
        // Don't edit out-of-stock products
        if ($product->quantity <= 0) {
            abort(404);
        }
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric',
        ]);

        // If quantity is being set to 0, delete the product
        if ($request->quantity <= 0) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $product->delete();
            return redirect()->route('products.index')->with('success', 'Product removed as it is now out of stock.');
        }

        $data = [
            'name' => $request->name,
            'quantity' => $request->quantity,
            'price' => $request->price,
        ];

        if ($request->hasFile('image')) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $imagePath = $request->file('image')->store('images/products', 'public');
            $data['image'] = $imagePath;
        }

        $product->update($data);

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function updateStock(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:0',
        ]);

        $product = Product::findOrFail($id);

        // If quantity is being set to 0, delete the product
        if ($request->quantity <= 0) {
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            $product->delete();
            return redirect()->route('products.index')->with('message', 'Product removed as it is now out of stock.');
        }

        $product->quantity = $request->quantity;
        $product->save();

        return redirect()->route('products.index')->with('message', 'Stock updated successfully.');
    }

    public function destroy(Product $product)
    {
        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
