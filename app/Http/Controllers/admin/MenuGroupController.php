<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\MenuGroup;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MenuGroupController extends Controller
{
    public function index()
    {
        $groups = MenuGroup::orderBy('order')->get();

        return view('backend.pages.menu_groups.index', compact('groups'));
    }

    public function storeAjax(Request $request)
    {
        $data = $this->validatedGroupData($request);
        $group = MenuGroup::create($data);

        return response()->json([
            'status' => 'success',
            'group' => $group,
        ]);
    }

    public function updateAjax(Request $request, $id)
    {
        $group = MenuGroup::findOrFail($id);
        $group->update($this->validatedGroupData($request, $group));

        return response()->json([
            'status' => 'success',
            'group' => $group,
        ]);
    }

    public function deleteAjax($id)
    {
        MenuGroup::findOrFail($id)->delete();

        return response()->json([
            'status' => 'success',
        ]);
    }

    private function validatedGroupData(Request $request, ?MenuGroup $group = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('menu_groups', 'name')->ignore($group?->id)],
            'order' => 'required|integer|min:0',
            'status' => 'sometimes|boolean',
        ]);

        $data['status'] = $data['status'] ?? $group?->status ?? true;

        return $data;
    }
}
