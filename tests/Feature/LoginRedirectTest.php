<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class LoginRedirectTest extends TestCase
{
    use RefreshDatabase;

    public function test_warehouse_manager_is_redirected_to_orders_after_login(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $role = Role::create(['name' => 'warehouse-manager', 'guard_name' => 'web']);
        $role->givePermissionTo(Permission::create(['name' => 'order.view.all', 'guard_name' => 'web']));
        $user = User::factory()->create(['email' => 'warehouse@example.test']);
        $user->assignRole($role);

        $this->post(route('login.post'), [
            'login' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.orders.index'));
    }

    public function test_super_admin_keeps_the_existing_dashboard_redirect(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $user = User::factory()->create(['email' => 'admin@example.test']);
        $user->assignRole(Role::create(['name' => 'super-admin', 'guard_name' => 'web']));

        $this->post(route('login.post'), [
            'login' => $user->email,
            'password' => 'password',
        ])->assertRedirect(route('admin.dashboard'));
    }
}
