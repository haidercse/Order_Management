<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = Category::query();

        // search filter
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // pagination
        $categories = $query
            ->orderBy('sort_order')
            ->latest('id')
            ->paginate(10)
            ->withQueryString();

        // ajax request
        if ($request->ajax()) {

            return response()->json([
                'html' => view(
                    'backend.pages.categories.partials.table',
                    compact('categories')
                )->render()
            ]);
        }

        // normal page load
        return view(
            'backend.pages.categories.index',
            compact('categories')
        );
    }


    // store
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:categories,code',
            'status' => 'required|boolean',
            'sort_order' => 'required|integer|min:0',
        ]);

        $category = Category::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Category created successfully!',
            'data' => $category
        ]);
    }


    // edit
    public function edit($id)
    {
        $category = Category::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $category
        ]);
    }


    // update
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:100|unique:categories,code,' . $id,
            'status' => 'required|boolean',
            'sort_order' => 'required|integer|min:0',
        ]);

        $category->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Category updated successfully!'
        ]);
    }


    // destroy
    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        $category->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Category deleted successfully!'
        ]);
    }
}