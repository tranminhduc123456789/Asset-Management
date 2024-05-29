<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Store;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::whereHas('store', function($query) use ($request) {
            $query->where('user_id', $request->user()->id);
        })->paginate(10);

        return response()->json($products);
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'stock_quantity' => 'required|integer',
            'sku' => 'required|string|unique:products,sku',
            'store_id' => 'required|exists:stores,id',
        ]);

        $store = Store::findOrFail($request->store_id);

        if ($store->user_id !== $request->user()->id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $product = Product::create($request->all());

        return response()->json($product, 201);
    }

    public function show(Product $product)
    {
        $this->authorize('view', $product);
        return response()->json($product);
    }

    public function update(Request $request, Product $product)
    {
        
        $this->authorize('update', $product);
        $request->validate([
            'product_name' => 'sometimes|required|string|max:255',
            'description' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric',
            'stock_quantity' => 'sometimes|required|integer',
            'sku' => 'sometimes|required|string|unique:products,sku,' . $product->id,
            'store_id' => 'sometimes|required|exists:stores,id',
        ]);

        if ($request->store_id) {
            $store = Store::findOrFail($request->store_id);
            if ($store->user_id !== $request->user()->id) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
        }

        $product->update($request->all());

        return response()->json($product);
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);
        $product->delete();
        return response()->json(['message' => 'Product deleted successfully']);
    }
}
