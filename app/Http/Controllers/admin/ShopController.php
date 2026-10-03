<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $query = Shop::query();

        //search filter
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('manager_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('city', 'like', "%{$search}%");
            });
        }

       //status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        //pagisnation
        $shops = $query
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        //ajax request
        if ($request->ajax()) {

            return response()->json([
                'html' => view(
                    'backend.pages.shops.partials.table',
                    compact('shops')
                )->render()
            ]);
        }

        //normal page load
        return view(
            'backend.pages.shops.index',
            compact('shops')
        );
    }


    //store
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:100|unique:shops,code',
            'name' => 'required|string|max:255',
            'manager_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        $shop = Shop::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Shop created successfully!',
            'data' => $shop
        ]);
    }


   //edit
    public function edit($id)
    {
        $shop = Shop::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $shop
        ]);
    }


    
    public function update(Request $request, $id)
    {
        $shop = Shop::findOrFail($id);

        $validated = $request->validate([
            'code' => 'required|string|max:100|unique:shops,code,' . $id,
            'name' => 'required|string|max:255',
            'manager_name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'status' => 'required|in:active,inactive',
            'notes' => 'nullable|string',
        ]);

        $shop->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Shop updated successfully!'
        ]);
    }


    
    public function destroy($id)
    {
        $shop = Shop::findOrFail($id);

        $shop->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Shop deleted successfully!'
        ]);
    }
}