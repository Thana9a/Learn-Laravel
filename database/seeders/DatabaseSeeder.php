<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles and permissions first
        $this->call([
            RolePermissionSeeder::class,
            ProductSeeder::class,
        ]);

        // Create test users with roles
        $admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);
        $admin->assignRole('admin');

        $manager = User::factory()->create([
            'name' => 'Manager User',
            'email' => 'manager@test.com',
            'password' => bcrypt('password'),
        ]);
        $manager->assignRole('manager');

        $staff = User::factory()->create([
            'name' => 'Staff User',
            'email' => 'staff@test.com',
            'password' => bcrypt('password'),
        ]);
        $staff->assignRole('staff');

        $this->command->info('Test users created:');
        $this->command->info('- admin@test.com / password (Admin)');
        $this->command->info('- manager@test.com / password (Manager)');
        $this->command->info('- staff@test.com / password (Staff)');
    }
}
