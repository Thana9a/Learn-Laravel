<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles and permissions
        $this->seed(\Database\Seeders\RolePermissionSeeder::class);
    }

    public function test_staff_cannot_access_reports_page(): void
    {
        $staff = User::factory()->create();
        $staff->assignRole('staff');

        $response = $this->actingAs($staff)->get(route('reports.index'));

        $response->assertStatus(403);
    }

    public function test_manager_can_view_reports(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $response = $this->actingAs($manager)->get(route('reports.index'));
        $response->assertStatus(200);
    }

    public function test_manager_cannot_access_settings(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        // Verify manager doesn't have settings.manage permission
        $this->assertFalse($manager->hasPermissionTo('settings.manage'));
    }

    public function test_admin_can_access_everything(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        // Admin can access dashboard
        $response = $this->actingAs($admin)->get(route('dashboard'));
        $response->assertStatus(200);

        // Admin can access reports
        $response = $this->actingAs($admin)->get(route('reports.index'));
        $response->assertStatus(200);

        // Admin can access products
        $response = $this->actingAs($admin)->get(route('products.index'));
        $response->assertStatus(200);

        // Admin can access orders
        $response = $this->actingAs($admin)->get(route('orders.index'));
        $response->assertStatus(200);
    }

    public function test_staff_can_access_pos_terminal(): void
    {
        $staff = User::factory()->create();
        $staff->assignRole('staff');

        $response = $this->actingAs($staff)->get(route('pos.index'));
        $response->assertStatus(200);
    }

    public function test_staff_can_view_orders(): void
    {
        $staff = User::factory()->create();
        $staff->assignRole('staff');

        $response = $this->actingAs($staff)->get(route('orders.index'));
        $response->assertStatus(200);
    }

    public function test_staff_can_view_products(): void
    {
        $staff = User::factory()->create();
        $staff->assignRole('staff');

        $response = $this->actingAs($staff)->get(route('products.index'));
        $response->assertStatus(200);
    }

    public function test_staff_cannot_create_products(): void
    {
        $staff = User::factory()->create();
        $staff->assignRole('staff');

        $productData = [
            'name' => 'Test Product',
            'price' => 10.00,
            'category' => 'test',
            'stock' => 100,
        ];

        $response = $this->actingAs($staff)->post(route('products.store'), $productData);
        $response->assertStatus(403);
    }

    public function test_staff_cannot_update_products(): void
    {
        $staff = User::factory()->create();
        $staff->assignRole('staff');

        $this->seed(\Database\Seeders\ProductSeeder::class);

        $productData = [
            'name' => 'Updated Product',
            'price' => 15.00,
        ];

        $response = $this->actingAs($staff)->put(route('products.update', 1), $productData);
        $response->assertStatus(403);
    }

    public function test_staff_cannot_delete_products(): void
    {
        $staff = User::factory()->create();
        $staff->assignRole('staff');

        $this->seed(\Database\Seeders\ProductSeeder::class);

        $response = $this->actingAs($staff)->delete(route('products.destroy', 1));
        $response->assertStatus(403);
    }

    public function test_admin_has_all_permissions(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $expectedPermissions = [
            'dashboard.view',
            'product.view',
            'product.create',
            'product.update',
            'product.delete',
            'order.create',
            'order.view',
            'order.void',
            'order.refund',
            'discount.apply',
            'discount.manage',
            'report.view',
            'report.export',
            'user.manage',
            'settings.manage',
        ];

        foreach ($expectedPermissions as $permission) {
            $this->assertTrue(
                $admin->hasPermissionTo($permission),
                "Admin should have permission: {$permission}"
            );
        }
    }

    public function test_staff_has_correct_permissions(): void
    {
        $staff = User::factory()->create();
        $staff->assignRole('staff');

        $allowedPermissions = [
            'dashboard.view',
            'order.create',
            'order.view',
            'product.view',
            'discount.apply',
        ];

        $deniedPermissions = [
            'product.create',
            'product.update',
            'product.delete',
            'order.void',
            'order.refund',
            'discount.manage',
            'report.view',
            'report.export',
            'user.manage',
            'settings.manage',
        ];

        foreach ($allowedPermissions as $permission) {
            $this->assertTrue(
                $staff->hasPermissionTo($permission),
                "Staff should have permission: {$permission}"
            );
        }

        foreach ($deniedPermissions as $permission) {
            $this->assertFalse(
                $staff->hasPermissionTo($permission),
                "Staff should NOT have permission: {$permission}"
            );
        }
    }

    public function test_manager_has_correct_permissions(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole('manager');

        $allowedPermissions = [
            'dashboard.view',
            'order.create',
            'order.view',
            'order.void',
            'order.refund',
            'product.view',
            'discount.apply',
            'report.view',
        ];

        $deniedPermissions = [
            'product.create',
            'product.update',
            'product.delete',
            'discount.manage',
            'report.export',
            'user.manage',
            'settings.manage',
        ];

        foreach ($allowedPermissions as $permission) {
            $this->assertTrue(
                $manager->hasPermissionTo($permission),
                "Manager should have permission: {$permission}"
            );
        }

        foreach ($deniedPermissions as $permission) {
            $this->assertFalse(
                $manager->hasPermissionTo($permission),
                "Manager should NOT have permission: {$permission}"
            );
        }
    }

    public function test_unauthenticated_user_cannot_access_protected_routes(): void
    {
        $response = $this->get(route('dashboard'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('reports.index'));
        $response->assertRedirect(route('login'));

        $response = $this->get(route('orders.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_inertia_shares_user_permissions_and_role(): void
    {
        $staff = User::factory()->create();
        $staff->assignRole('staff');

        $response = $this->actingAs($staff)->get(route('dashboard'));

        $response->assertStatus(200);

        // Check that Inertia props contain permissions and role
        $props = $response->viewData('page')['props'];
        
        $this->assertArrayHasKey('auth', $props);
        $this->assertArrayHasKey('permissions', $props['auth']);
        $this->assertArrayHasKey('role', $props['auth']);
        $this->assertEquals('staff', $props['auth']['role']);
        $this->assertIsArray($props['auth']['permissions']);
        $this->assertContains('dashboard.view', $props['auth']['permissions']);
    }

    public function test_staff_can_apply_discounts(): void
    {
        $staff = User::factory()->create();
        $staff->assignRole('staff');

        $this->assertTrue($staff->hasPermissionTo('discount.apply'));
    }

    public function test_staff_cannot_manage_discounts(): void
    {
        $staff = User::factory()->create();
        $staff->assignRole('staff');

        $this->assertFalse($staff->hasPermissionTo('discount.manage'));
    }

    public function test_admin_can_manage_discounts(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $this->assertTrue($admin->hasPermissionTo('discount.manage'));
    }
}
