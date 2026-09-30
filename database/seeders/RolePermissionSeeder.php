<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Permission Groups
        $groups = [
            'Menu Management',
            'Role Management',
            'Permission Management',
        ];

        foreach ($groups as $group) {
            DB::table('permission_groups')->insert(['name' => $group]);
        }

        // Permissions
        $permissions = [
            'Menu Management' => [
                'menu.view',
                'menu.create',
                'menu.edit',
                'menu.delete'
            ],
            'Role Management' => [
                'role.view',
                'role.create',
                'role.edit',
                'role.delete'
            ],
            'Permission Management' => [
                'permission.view',
                'permission.create',
                'permission.edit',
                'permission.delete'
            ]
        ];

        foreach ($permissions as $group => $perms) {
            $groupId = DB::table('permission_groups')->where('name', $group)->value('id');

            foreach ($perms as $perm) {
                Permission::create([
                    'name' => $perm,
                    'group_id' => $groupId
                ]);
            }
        }

        // Roles
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $agent = Role::firstOrCreate(['name' => 'agent']);

        // Super Admin gets all permissions
        $superAdmin->syncPermissions(Permission::all());

        // Admin gets limited permissions
        $admin->givePermissionTo([
            'permission.view',
            'permission.create',
            'permission.edit',
            'permission.delete'
        ]);

        // Agent gets booking permissions only
        $agent->givePermissionTo([
            'permission.view',
            'permission.create',
            'permission.edit',
            'permission.delete'
        ]);
    }
}