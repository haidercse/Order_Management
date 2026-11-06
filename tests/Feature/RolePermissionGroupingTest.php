<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class RolePermissionGroupingTest extends TestCase
{
    use RefreshDatabase;

    public function test_role_permission_editor_groups_permissions_by_their_permission_group(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $menuGroupId = DB::table('permission_groups')->insertGetId([
            'name' => 'Menu Management',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $categoryGroupId = DB::table('permission_groups')->insertGetId([
            'name' => 'Category Management',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Permission::create([
            'name' => 'role.view',
            'guard_name' => 'web',
        ]);
        Permission::create([
            'name' => 'menu.view',
            'guard_name' => 'web',
            'group_id' => $menuGroupId,
        ]);
        Permission::create([
            'name' => 'menu.create',
            'guard_name' => 'web',
            'group_id' => $menuGroupId,
        ]);
        Permission::create([
            'name' => 'category.view',
            'guard_name' => 'web',
            'group_id' => $categoryGroupId,
        ]);

        $role = Role::create(['name' => 'warehouse-manager', 'guard_name' => 'web']);
        $role->givePermissionTo('role.view');
        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)
            ->get(route('admin.roles.index'))
            ->assertOk()
            ->assertSee('Menu Management')
            ->assertSee('Category Management')
            ->assertSee('menu.view')
            ->assertSee('menu.create')
            ->assertSee('category.view')
            ->assertSee('class="col-12 col-sm-6 col-xl-3 mb-3"', false)
            ->assertSee('class="custom-control-input group-check"', false)
            ->assertSee('class="custom-control-input perm-checkbox"', false);
    }
}
