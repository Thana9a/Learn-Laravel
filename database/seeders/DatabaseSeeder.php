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
        // 1. Roles and permissions
        // 2. Products and catalog
        // 3. System and test users
        $this->call([
            RolePermissionSeeder::class,
            ProductSeeder::class,
            UserSeeder::class,
        ]);
    }
}
