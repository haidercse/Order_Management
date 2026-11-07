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
        $data = $this->validatedMenuData($request);
        $menu = Menu::create($data);

        return response()->json(['status' => 'success', 'menu' => $menu]);
    }

    public function updateAjax(Request $request, $id)
    {
        $menu = Menu::findOrFail($id);
        $menu->update($this->validatedMenuData($request, $menu));

        return response()->json(['status' => 'success', 'menu' => $menu]);
    }

    public function deleteAjax($id)
    {
        Menu::findOrFail($id)->delete();

        return response()->json(['status' => 'success']);
    }

    private function validatedMenuData(Request $request, ?Menu $menu = null): array
    {
        $data = $request->validate([
            'group_id' => 'required_without:parent_id|nullable|integer|exists:menu_groups,id',
            'parent_id' => 'nullable|integer|exists:menus,id',
            'title' => 'required|string|max:255',
            'route' => 'nullable|string|max:255',
            'icon' => 'nullable|string|max:255',
            'permission' => 'nullable|string|max:255',
            'order' => 'required|integer|min:0',
            'status' => 'sometimes|boolean',
        ]);

        if (! empty($data['parent_id'])) {
            $parent = Menu::whereNull('parent_id')->find($data['parent_id']);
            if (! $parent || $parent->is($menu)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'parent_id' => 'Select a different top-level menu as the parent.',
                ]);
            }
            if ($menu?->children()->exists()) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'parent_id' => 'A menu with child menus cannot be moved under another menu.',
                ]);
            }

            $data['group_id'] = $parent->group_id;
        }

        if (! array_key_exists('status', $data)) {
            $data['status'] = $menu?->status ?? true;
        }

        return $data;
    }
}
