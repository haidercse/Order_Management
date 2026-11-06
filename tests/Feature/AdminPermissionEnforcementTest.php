<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\MenuSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AdminPermissionEnforcementTest extends TestCase
{
    use RefreshDatabase;

    public function test_warehouse_manager_can_only_open_and_see_pages_the_role_can_view(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $role = Role::create(['name' => 'warehouse-manager', 'guard_name' => 'web']);
        $role->givePermissionTo(Permission::create(['name' => 'user.view', 'guard_name' => 'web']));
        $user = User::factory()->create();
        $user->assignRole($role);
        $this->seed(MenuSeeder::class);

        $this->actingAs($user)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee(route('admin.users.index'))
            ->assertDontSee(route('admin.shops.index'))
            ->assertDontSee(route('admin.categories.index'))
            ->assertDontSee(route('admin.dashboard'));

        $this->actingAs($user)
            ->get(route('admin.categories.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('admin.roles.index'))
            ->assertForbidden();
    }

    public function test_order_status_requires_permission_for_the_requested_transition(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $role = Role::create(['name' => 'warehouse-manager', 'guard_name' => 'web']);
        $role->givePermissionTo(Permission::create(['name' => 'order.ready', 'guard_name' => 'web']));
        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)
            ->post(route('admin.orders.status', ['id' => 1]), ['status' => 'prepared'])
            ->assertForbidden();
    }

    public function test_warehouse_manager_landing_page_uses_an_assigned_view_permission(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $role = Role::create(['name' => 'warehouse-manager', 'guard_name' => 'web']);
        $role->givePermissionTo(Permission::create(['name' => 'category.view', 'guard_name' => 'web']));
        $user = User::factory()->create();
        $user->assignRole($role);
        $this->seed(MenuSeeder::class);

        $this->actingAs($user)
            ->get(route('home'))
            ->assertRedirect(route('admin.categories.index'));

        $this->actingAs($user)
            ->get(route('admin.categories.index'))
            ->assertOk()
            ->assertSee(route('admin.categories.index'))
            ->assertDontSee(route('admin.fruits.index'));
    }

    public function test_warehouse_dashboard_is_permission_protected_and_admin_dashboard_is_super_admin_only(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $role = Role::create(['name' => 'warehouse-manager', 'guard_name' => 'web']);
        $role->givePermissionTo(Permission::create(['name' => 'inventory.view', 'guard_name' => 'web']));
        $user = User::factory()->create();
        $user->assignRole($role);

        $this->actingAs($user)
            ->get(route('home'))
            ->assertRedirect(route('admin.warehouse.dashboard'));

        $this->actingAs($user)
            ->get(route('admin.warehouse.dashboard'))
            ->assertOk()
            ->assertDontSee("Today's shop orders")
            ->assertDontSee("Today's submitted orders")
            ->assertDontSee('Warehouse availability &amp; shortage');

        $this->actingAs($user)
            ->get(route('admin.dashboard'))
            ->assertForbidden();
    }
}
