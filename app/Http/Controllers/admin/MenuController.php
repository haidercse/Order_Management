<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\MenuGroup;
use Illuminate\Http\Request;

class MenuController extends Controller
{
    public function index(Request $request)
{
    $groups = MenuGroup::orderBy('order')->get();

    $menus = Menu::with('parent', 'group')
        ->orderBy('group_id')                 // group wise
        ->orderByRaw('parent_id IS NOT NULL') // parent আগে, child পরে
        ->orderBy('parent_id')                // same parent এর child গুলো একসাথে
        ->orderBy('order')                    // menu order
        ->orderBy('id')                       // fallback (important)
        ->get();

    $parents = Menu::whereNull('parent_id')->orderBy('title')->get();

    return view('backend.pages.menus.index', compact('menus', 'groups', 'parents'));
}


    public function storeAjax(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'order' => 'required|integer',
        ]);

        $menu = new Menu();

        // If child → inherit group from parent
        if ($request->parent_id) {
            $parent = Menu::find($request->parent_id);
            $menu->group_id = $parent->group_id;
        } else {
            $menu->group_id = $request->group_id;
        }

        $menu->parent_id = $request->parent_id;
        $menu->title = $request->title;
        $menu->route = $request->route;
        $menu->permission = $request->permission;
        $menu->order = $request->order;

        $menu->save();

        return response()->json(['status' => 'success']);
    }

    public function updateAjax(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);

        if ($request->parent_id) {
            $parent = Menu::find($request->parent_id);
            $menu->group_id = $parent->group_id;
        } else {
            $menu->group_id = $request->group_id;
        }

        $menu->parent_id = $request->parent_id;
        $menu->title = $request->title;
        $menu->route = $request->route;
        $menu->permission = $request->permission;
        $menu->order = $request->order;

        $menu->save();

        return response()->json(['status' => 'success']);
    }

    public function deleteAjax($id)
    {
        Menu::findOrFail($id)->delete();

        return response()->json(['status' => 'success']);
    }
}