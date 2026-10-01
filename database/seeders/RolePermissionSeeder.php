<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            'Menu Management' => ['menu.view','menu.create','menu.edit','menu.delete'],
            'Role Management' => ['role.view','role.create','role.edit','role.delete'],
            'Permission Management' => ['permission.view','permission.create','permission.edit','permission.delete'],
            'Shop Management' => ['shop.view','shop.create','shop.edit','shop.delete'],
            'User Management' => ['user.view','user.create','user.edit','user.delete'],
            'Fruit Management' => [
                'category.view','category.create','category.edit','category.delete',
                'fruit.view','fruit.create','fruit.edit','fruit.delete',
                'unit.view','unit.create','unit.edit','unit.delete',
                'box.view','box.create','box.edit','box.delete',
                'price.view','price.create','price.edit','price.delete',
            ],
            'Order Management' => [
                'order.view.own','order.create.own','order.edit.own','order.submit.own',
                'order.view.all','order.prepare','order.ready','order.send',
            ],
            'Inventory Management' => [
                'inventory.view','inventory.create','inventory.edit','inventory.adjust',
            ],
            'Report Management' => [
                'report.view','report.pdf','report.excel',
            ],
        ];

        foreach ($permissions as $groupName => $permissionNames) {
            $groupId = DB::table('permission_groups')->where('name', $groupName)->value('id');

            foreach ($permissionNames as $name) {
                Permission::updateOrCreate(
                    ['name' => $name, 'guard_name' => 'web'],
                    ['group_id' => $groupId]
                );
            }
        }

        $superAdmin = Role::firstOrCreate(
            ['name' => 'super-admin', 'guard_name' => 'web']
        );

        $warehouse = Role::firstOrCreate(
            ['name' => 'warehouse-manager', 'guard_name' => 'web']
        );

        $shopManager = Role::firstOrCreate(
            ['name' => 'shop-manager', 'guard_name' => 'web']
        );

        if ($superAdmin->wasRecentlyCreated) {
            $superAdmin->syncPermissions(Permission::all());
        }

        if ($warehouse->wasRecentlyCreated) {
            $warehouse->syncPermissions(
                Permission::whereIn('name', array_merge(
                    $permissions['Shop Management'],
                    $permissions['Fruit Management'],
                    $permissions['Order Management'],
                    $permissions['Inventory Management'],
                    $permissions['Report Management']
                ))->get()
            );
        }

        if ($shopManager->wasRecentlyCreated) {
            $shopManager->syncPermissions(
                Permission::whereIn('name', [
                    'order.view.own',
                    'order.create.own',
                    'order.edit.own',
                    'order.submit.own',
                ])->get()
            );
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
}
