<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\MenuGroup;
use Illuminate\Http\Request;

class MenuGroupController extends Controller
{
    public function index()
    {
        $groups = MenuGroup::orderBy('order')->get();
        return view('backend.pages.menu_groups.index', compact('groups'));
    }

    public function storeAjax(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'order' => 'required|integer',
        ]);

        $group = MenuGroup::create($request->all());

        return response()->json([
            'status' => 'success',
            'group' => $group
        ]);
    }

    public function updateAjax(Request $request, $id)
    {
        $group = MenuGroup::findOrFail($id);

        $group->update($request->all());

        return response()->json([
            'status' => 'success',
            'group' => $group
        ]);
    }

    public function deleteAjax($id)
    {
        MenuGroup::findOrFail($id)->delete();

        return response()->json([
            'status' => 'success'
        ]);
    }
}