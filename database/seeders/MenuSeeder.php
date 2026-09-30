<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menuGroup = DB::table('menu_groups')->insertGetId([
            'name' => 'Menu Management',
            'order' => 1
        ]);

        // Parent Menu
        $menuParent = DB::table('menus')->insertGetId([
            'group_id' => $menuGroup,
            'title' => 'Menus',
            'icon' => 'ti-menu',
            'permission' => 'menu.view',
            'order' => 1
        ]);

        // Submenus
        DB::table('menus')->insert([
            [
                'parent_id' => $menuParent,
                'title' => 'Menu List',
                'route' => 'admin.menus.index',
                'permission' => 'menu.view',
                'order' => 1
            ],
            [
                'parent_id' => $menuParent,
                'title' => 'Menu Group',
                'route' => 'admin.menu-groups.index',
                'permission' => 'menu.view',
                'order' => 1
            ],
            
        ]);


        $roleGroup = DB::table('menu_groups')->insertGetId([
            'name' => 'Role Management',
            'order' => 2
        ]);

        $roleParent = DB::table('menus')->insertGetId([
            'group_id' => $roleGroup,
            'title' => 'Roles',
            'icon' => 'ti-lock',
            'permission' => 'role.view',
            'order' => 1
        ]);

        DB::table('menus')->insert([
            [
                'parent_id' => $roleParent,
                'title' => 'Role List',
                'route' => 'admin.roles.index',
                'permission' => 'role.view',
                'order' => 1
            ],
            
        ]);

        $permissionGroup = DB::table('menu_groups')->insertGetId([
            'name' => 'Permission Management',
            'order' => 3
        ]);

        $permissionParent = DB::table('menus')->insertGetId([
            'group_id' => $permissionGroup,
            'title' => 'Permissions',
            'icon' => 'ti-key',
            'permission' => 'permission.view',
            'order' => 1
        ]);

        DB::table('menus')->insert([
            [
                'parent_id' => $permissionParent,
                'title' => 'Permissions List',
                'route' => 'admin.permissions.index',
                'permission' => 'permission.view',
                'order' => 1
            ],
          
        ]);
    }
}