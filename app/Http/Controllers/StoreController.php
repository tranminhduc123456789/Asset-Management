<?php

namespace App\Http\Controllers;

use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request)
    {
        $stores = Store::where('user_id', $request->user()->id)->paginate(10);
        return response()->json($stores);
    }

    public function store(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'store_address' => 'required|string|max:255',
            'store_phone' => 'required|string|max:15',
            'store_email' => 'required|string|email|max:255',
        ]);

        $store = Store::create([
            'store_name' => $request->store_name,
            'store_address' => $request->store_address,
            'store_phone' => $request->store_phone,
            'store_email' => $request->store_email,
            'user_id' => $request->user()->id,
        ]);

        return response()->json($store, 201);
    }

    public function show(Store $store)
    {
        $this->authorize('view', $store);
        return response()->json($store);
    }

    public function update(Request $request, Store $store)
    {
        $this->authorize('update', $store);

        $request->validate([
            'store_name' => 'sometimes|required|string|max:255',
            'store_address' => 'sometimes|required|string|max:255',
            'store_phone' => 'sometimes|required|string|max:15',
            'store_email' => 'sometimes|required|string|email|max:255',
        ]);

        $store->update($request->all());

        return response()->json($store);
    }

    public function destroy(Store $store)
    {
        $this->authorize('delete', $store);
        $store->delete();

        return response()->json(['message' => 'Store deleted successfully']);
    }
}
