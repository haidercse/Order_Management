<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    public function run(): void
    {
        $menus = [
            'Dashboard' => [
                ['title'=>'Dashboard','icon'=>'ti-dashboard','route'=>'admin.dashboard','children'=>[]],
            ],
            'Warehouse Management' => [
                ['title'=>'Warehouse','icon'=>'ti-package','permission'=>'inventory.view','children'=>[
                    ['title'=>'Warehouse Dashboard','route'=>'admin.warehouse.dashboard','permission'=>'inventory.view'],
                    ['title'=>'Inventory','route'=>'admin.inventory.index','permission'=>'inventory.view'],
                    ['title'=>'Shop Orders','route'=>'admin.orders.index','permission'=>'order.view.all'],
                    ['title'=>'Shortage / Purchase','route'=>'admin.shortage.index','permission'=>'inventory.view'],
                ]],
            ],
            'Shop Management' => [
                ['title'=>'Shops','icon'=>'fa fa-shopping-cart','permission'=>'shop.view','children'=>[
                    ['title'=>'Shop List','route'=>'admin.shops.index','permission'=>'shop.view'],
                ]],
            ],
            'Master Data' => [
                ['title'=>'Fruits','icon'=>'ti-apple','permission'=>'fruit.view','children'=>[
                    ['title'=>'Fruit List','route'=>'admin.fruits.index','permission'=>'fruit.view'],
                    ['title'=>'Categories','route'=>'admin.categories.index','permission'=>'category.view'],
                    ['title'=>'Box Configuration','route'=>'admin.boxes.index','permission'=>'box.view'],
                    ['title'=>'Prices','route'=>'admin.fruit-prices.index','permission'=>'price.view'],
                ]],
            ],
            'Reports' => [
                ['title'=>'Reports','icon'=>'fa fa-file-text-o','permission'=>'report.view','children'=>[
                    ['title'=>'Daily Warehouse Report','route'=>'admin.reports.daily','permission'=>'report.view'],
                    ['title'=>'PDF Reports','route'=>'admin.reports.pdf','permission'=>'report.pdf'],
                    ['title'=>'Excel Reports','route'=>'admin.reports.excel','permission'=>'report.excel'],
                ]],
            ],
            'Role Management' => [
                ['title'=>'Roles','icon'=>'ti-lock','permission'=>'role.view','children'=>[
                    ['title'=>'Role List','route'=>'admin.roles.index','permission'=>'role.view'],
                ]],
            ],
            'Menu Management' => [
                ['title'=>'Menus','icon'=>'ti-menu','permission'=>'menu.view','children'=>[
                    ['title'=>'Menu List','route'=>'admin.menus.index','permission'=>'menu.view'],
                    ['title'=>'Menu Group','route'=>'admin.menu-groups.index','permission'=>'menu.view'],
                ]],
            ],
            'Permission Management' => [
                ['title'=>'Permissions','icon'=>'ti-key','permission'=>'permission.view','children'=>[
                    ['title'=>'Permissions List','route'=>'admin.permissions.index','permission'=>'permission.view'],
                ]],
            ],
        ];

        $groupOrder = 1;
        foreach ($menus as $groupName => $parents) {
            $groupId = DB::table('menu_groups')->updateOrInsert(
                ['name' => $groupName],
                ['order' => $groupOrder, 'status' => true, 'updated_at' => now(), 'created_at' => now()]
            );
            $groupId = DB::table('menu_groups')->where('name', $groupName)->value('id');

            $parentOrder = 1;
            foreach ($parents as $parent) {
                $parentKey = [
                    'group_id' => $groupId,
                    'parent_id' => null,
                    'title' => $parent['title'],
                ];
                DB::table('menus')->updateOrInsert($parentKey, [
                    'route' => $parent['route'] ?? null,
                    'icon' => $parent['icon'] ?? null,
                    'permission' => $parent['permission'] ?? null,
                    'order' => $parentOrder++,
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
                $parentId = DB::table('menus')
                    ->where('group_id', $groupId)
                    ->whereNull('parent_id')
                    ->where('title', $parent['title'])
                    ->value('id');

                $childOrder = 1;
                foreach ($parent['children'] as $child) {
                    DB::table('menus')->updateOrInsert(
                        [
                            'group_id' => $groupId,
                            'parent_id' => $parentId,
                            'title' => $child['title'],
                        ],
                        [
                            'route' => $child['route'] ?? null,
                            'icon' => null,
                            'permission' => $child['permission'] ?? null,
                            'order' => $childOrder++,
                            'status' => true,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }
            $groupOrder++;
        }
    }
}
