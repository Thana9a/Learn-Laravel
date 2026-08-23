<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define all permissions grouped by resource
        $permissions = [
            // Dashboard
            'dashboard.view',

            // Products
            'product.view',
            'product.create',
            'product.update',
            'product.delete',

            // Orders
            'order.create',
            'order.view',
            'order.void',
            'order.refund',

            // Discounts
            'discount.apply',
            'discount.manage',

            // Reports
            'report.view',
            'report.export',

            // Users
            'user.manage',

            // Settings
            'settings.manage',
        ];

        // Create all permissions
        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission, 'guard_name' => 'web']);
        }

        // Clear cache again after creating permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles and assign permissions
        
        // Staff role - basic POS operations
        $staffRole = Role::create(['name' => 'staff', 'guard_name' => 'web']);
        $staffRole->givePermissionTo([
            'dashboard.view',
            'order.create',
            'order.view',
            'product.view',
            'discount.apply',
        ]);

        // Manager role - staff permissions + supervisory capabilities
        $managerRole = Role::create(['name' => 'manager', 'guard_name' => 'web']);
        $managerRole->givePermissionTo([
            'dashboard.view',
            'order.create',
            'order.view',
            'order.void',
            'order.refund',
            'product.view',
            'discount.apply',
            'report.view',
        ]);

        // Admin role - all permissions
        $adminRole = Role::create(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->givePermissionTo(Permission::all());

        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('- Staff: ' . $staffRole->permissions->count() . ' permissions');
        $this->command->info('- Manager: ' . $managerRole->permissions->count() . ' permissions');
        $this->command->info('- Admin: ' . $adminRole->permissions->count() . ' permissions');
    }
}
