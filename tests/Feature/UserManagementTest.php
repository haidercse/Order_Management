<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\MenuSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_management_menu_is_seeded(): void
    {
        $this->seed(MenuSeeder::class);

        $this->assertDatabaseHas('menus', [
            'title' => 'User List',
            'route' => 'admin.users.index',
            'permission' => 'user.view',
        ]);
    }

    public function test_super_admin_can_create_a_user_with_a_selected_role(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $role = Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
        $role->givePermissionTo([
            Permission::create(['name' => 'user.view', 'guard_name' => 'web']),
            Permission::create(['name' => 'user.create', 'guard_name' => 'web']),
        ]);
        $admin = User::factory()->create();
        $admin->assignRole($role);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('Add User');

        $this->actingAs($admin)
            ->post(route('admin.users.store'), [
                'name' => 'New Warehouse User',
                'email' => 'warehouse.user@example.com',
                'password' => 'password123',
                'role' => 'super-admin',
            ])
            ->assertRedirect(route('admin.users.index'));

        $user = User::where('email', 'warehouse.user@example.com')->firstOrFail();
        $this->assertSame('New Warehouse User', $user->name);
        $this->assertTrue(Hash::check('password123', $user->password));
        $this->assertTrue($user->hasRole('super-admin'));
    }

    public function test_shop_manager_role_cannot_be_created_without_a_shop(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $role = Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
        $role->givePermissionTo([
            Permission::create(['name' => 'user.create', 'guard_name' => 'web']),
        ]);
        Role::create(['name' => 'shop-manager', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole($role);

        $this->actingAs($admin)
            ->from(route('admin.users.index'))
            ->post(route('admin.users.store'), [
                'name' => 'Unlinked Shop User',
                'email' => 'unlinked.shop@example.com',
                'password' => 'password123',
                'role' => 'shop-manager',
            ])
            ->assertSessionHasErrors('role');

        $this->assertDatabaseMissing('users', ['email' => 'unlinked.shop@example.com']);
    }
}
