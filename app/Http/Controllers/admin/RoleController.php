<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        $groupNames = DB::table('permission_groups')->pluck('name', 'id');
        $permissions = Permission::query()
            ->orderBy('name')
            ->get()
            ->groupBy(fn (Permission $permission) => $permission->group_name
                ?: ($groupNames[$permission->group_id] ?? Str::headline(Str::before($permission->name, '.'))));

        return view('backend.pages.roles.index', compact('roles', 'permissions'));
    }

    public function getRole($id)
    {
        $role = Role::findOrFail($id);

        return response()->json([
            'role' => $role,
            'assigned' => $role->permissions->pluck('name')->toArray(),
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
