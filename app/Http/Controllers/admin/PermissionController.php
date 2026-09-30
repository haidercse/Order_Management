<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        // Grouped permissions for table
        $permissions = Permission::orderBy('group_name')->get()->groupBy('group_name');

        // Existing groups for dropdown
        $groups = Permission::select('group_name')
            ->whereNotNull('group_name')
            ->groupBy('group_name')
            ->pluck('group_name');

        return view('backend.pages.permissions.index', compact('permissions', 'groups'));
    }

    public function storeAjax(Request $request)
    {
        // Validate multiple permission names
        if (!$request->has('names') || count($request->names) == 0) {
            return response()->json(['message' => 'No permission names provided'], 422);
        }

        $groupName = $request->group_name;

        foreach ($request->names as $name) {
            if ($name != null && $name != "") {
                Permission::create([
                    'name' => $name,
                    'group_name' => $groupName,
                ]);
            }
        }

        return response()->json(['message' => 'Permissions created successfully']);
    }

    public function updateAjax(Request $request, $id)
    {
        $permission = Permission::findOrFail($id);

        // Check duplicate (except current row)
        $exists = Permission::where('name', $request->name)
            ->where('guard_name', 'web')
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return response()->json([
                'message' => "Permission '{$request->name}' already exists."
            ], 422);
        }

        $permission->update([
            'name' => $request->name,
            'group_name' => $request->group_name,
        ]);

        return response()->json(['message' => 'Permission updated successfully']);
    }

    public function deleteAjax($id)
    {
        Permission::findOrFail($id)->delete();

        return response()->json(['message' => 'Permission deleted']);
    }
}