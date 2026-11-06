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

    public function test_user_list_contains_all_roles_except_shop_managers(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $superAdminRole = Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdminRole->givePermissionTo(Permission::create(['name' => 'user.view', 'guard_name' => 'web']));
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $warehouseRole = Role::create(['name' => 'warehouse-manager', 'guard_name' => 'web']);
        $shopManagerRole = Role::create(['name' => 'shop-manager', 'guard_name' => 'web']);

        $admin = User::factory()->create(['name' => 'Listed Admin']);
        $admin->assignRole($superAdminRole);
        $regularAdmin = User::factory()->create(['name' => 'Listed Admin Role']);
        $regularAdmin->assignRole($adminRole);
        $warehouseUser = User::factory()->create(['name' => 'Hidden Warehouse User']);
        $warehouseUser->assignRole($warehouseRole);
        $shopManager = User::factory()->create(['name' => 'Hidden Shop Manager']);
        $shopManager->assignRole($shopManagerRole);

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('Listed Admin')
            ->assertSee('Listed Admin Role')
            ->assertSee('Hidden Warehouse User')
            ->assertDontSee('Hidden Shop Manager');
    }

    public function test_admin_user_can_be_updated_and_deleted_with_ajax(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $role = Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
        $role->givePermissionTo([
            Permission::create(['name' => 'user.view', 'guard_name' => 'web']),
            Permission::create(['name' => 'user.edit', 'guard_name' => 'web']),
            Permission::create(['name' => 'user.delete', 'guard_name' => 'web']),
        ]);
        $regularRole = Role::create(['name' => 'warehouse-manager', 'guard_name' => 'web']);
        $admin = User::factory()->create();
        $admin->assignRole($role);
        $target = User::factory()->create(['name' => 'Original Name']);
        $target->assignRole($regularRole);
        $headers = ['X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json'];

        $this->actingAs($admin)
            ->withHeaders($headers)
            ->post(route('admin.users.update.ajax', $target->id), [
                'name' => 'Updated Name',
                'email' => $target->email,
                'password' => '',
                'role' => 'warehouse-manager',
            ])
            ->assertOk()
            ->assertJson(['message' => 'User updated successfully.']);

        $this->assertDatabaseHas('users', ['id' => $target->id, 'name' => 'Updated Name']);

        $this->actingAs($admin)
            ->withHeaders($headers)
            ->delete(route('admin.users.destroy.ajax', $target->id))
            ->assertOk()
            ->assertJson(['message' => 'User deleted successfully.']);

        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }

    public function test_super_admin_cannot_be_deleted_and_has_no_delete_button(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $role = Role::create(['name' => 'super-admin', 'guard_name' => 'web']);
        $role->givePermissionTo([
            Permission::create(['name' => 'user.view', 'guard_name' => 'web']),
            Permission::create(['name' => 'user.delete', 'guard_name' => 'web']),
        ]);
        $admin = User::factory()->create();
        $admin->assignRole($role);
        $otherSuperAdmin = User::factory()->create(['name' => 'Protected Super Admin']);
        $otherSuperAdmin->assignRole($role);
        $headers = ['X-Requested-With' => 'XMLHttpRequest', 'Accept' => 'application/json'];

        $this->actingAs($admin)
            ->get(route('admin.users.index'))
            ->assertOk()
            ->assertSee('Protected Super Admin')
            ->assertDontSee('class="btn btn-danger btn-sm delete-user" data-id="'.$otherSuperAdmin->id.'"');

        $this->actingAs($admin)
            ->withHeaders($headers)
            ->delete(route('admin.users.destroy.ajax', $otherSuperAdmin->id))
            ->assertUnprocessable()
            ->assertJson(['message' => 'Super admin accounts cannot be deleted.']);

        $this->assertDatabaseHas('users', ['id' => $otherSuperAdmin->id]);
    }
}
