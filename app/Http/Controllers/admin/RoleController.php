<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        // group_name অনুযায়ী group হবে
        $roles = Role::all();
        $permissions = Permission::orderBy('group_name')->get()->groupBy('group_name');

        return view('backend.pages.roles.index', compact('roles', 'permissions'));
    }

    public function getRole($id)
    {
        $role = Role::findOrFail($id);

        return response()->json([
            'role' => $role,
            'assigned' => $role->permissions->pluck('name')->toArray()
        ]);
    }

    public function storeAjax(Request $request)
    {
        $role = Role::create(['name' => $request->name]);

        $role->syncPermissions($request->permissions ?? []);

        return response()->json(['message' => 'Role created successfully']);
    }

    public function updateAjax(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $role->update(['name' => $request->name]);

        $role->syncPermissions($request->permissions ?? []);

        return response()->json(['message' => 'Role updated successfully']);
    }

    public function deleteAjax($id)
    {
        Role::findOrFail($id)->delete();

        return response()->json(['message' => 'Role deleted']);
    }
}