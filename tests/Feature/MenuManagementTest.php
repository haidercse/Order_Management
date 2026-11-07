<?php

namespace Tests\Feature;

use App\Models\Menu;
use App\Models\MenuGroup;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_management_pages_render_icon_suggestions_and_menu_group_status_form(): void
    {
        $this->actingAs(User::factory()->create())
            ->withoutMiddleware()
            ->get(route('admin.menus.index'))
            ->assertOk()
            ->assertSee('name="icon"', false)
            ->assertSee('fa fa-users')
            ->assertSee('name="permission"', false)
            ->assertSee('name="status"', false);

        $this->actingAs(User::factory()->create())
            ->withoutMiddleware()
            ->get(route('admin.menu-groups.index'))
            ->assertOk()
            ->assertSee('name="status"', false);
    }

    public function test_menu_forms_save_icons_permissions_status_and_parent_group_consistently(): void
    {
        $group = MenuGroup::create([
            'name' => 'Warehouse Management',
            'order' => 1,
            'status' => true,
        ]);
        $parentPayload = [
            'group_id' => $group->id,
            'title' => 'Warehouse',
            'route' => 'admin.warehouse.dashboard',
            'icon' => 'ti-package',
            'permission' => 'inventory.view',
            'order' => 2,
            'status' => true,
        ];

        $this->withoutMiddleware()
            ->postJson(route('admin.menus.store.ajax'), $parentPayload)
            ->assertOk()
            ->assertJsonPath('status', 'success');

        $parent = Menu::where('title', 'Warehouse')->firstOrFail();
        $this->assertDatabaseHas('menus', [
            'id' => $parent->id,
            'group_id' => $group->id,
            'icon' => 'ti-package',
            'permission' => 'inventory.view',
            'status' => true,
        ]);

        $this->withoutMiddleware()
            ->postJson(route('admin.menus.update.ajax', $parent), [
                ...$parentPayload,
                'icon' => 'fa fa-cubes',
                'permission' => 'shop.view',
                'status' => false,
            ])
            ->assertOk()
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseHas('menus', [
            'id' => $parent->id,
            'icon' => 'fa fa-cubes',
            'permission' => 'shop.view',
            'status' => false,
        ]);

        $this->withoutMiddleware()
            ->postJson(route('admin.menus.store.ajax'), [
                'parent_id' => $parent->id,
                'title' => 'Inventory',
                'route' => 'admin.inventory.index',
                'order' => 1,
                'status' => true,
            ])
            ->assertOk()
            ->assertJsonPath('status', 'success');

        $this->assertDatabaseHas('menus', [
            'title' => 'Inventory',
            'group_id' => $group->id,
            'parent_id' => $parent->id,
        ]);
    }

    public function test_menu_group_validation_allows_editing_its_name_and_rejects_duplicate_names(): void
    {
        $group = MenuGroup::create(['name' => 'Old name', 'order' => 1, 'status' => true]);
        MenuGroup::create(['name' => 'Existing name', 'order' => 2, 'status' => true]);

        $this->withoutMiddleware()
            ->postJson(route('admin.menu-groups.update.ajax', $group), [
                'name' => 'Old name',
                'order' => 3,
                'status' => false,
            ])
            ->assertOk()
            ->assertJsonPath('status', 'success');

        $this->withoutMiddleware()
            ->postJson(route('admin.menu-groups.update.ajax', $group), [
                'name' => 'Existing name',
                'order' => 3,
                'status' => true,
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('name');

        $this->assertDatabaseHas('menu_groups', [
            'id' => $group->id,
            'order' => 3,
            'status' => false,
        ]);
    }
}
