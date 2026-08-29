<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Super Admin User
        $superAdmin = User::updateOrCreate(
            ['email' => 'thanapro70@gmail.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('Tukbuy64@'),
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->syncRoles(['admin']);

        // 2. Default Test Users
        $admin = User::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles(['admin']);

        $manager = User::updateOrCreate(
            ['email' => 'manager@test.com'],
            [
                'name' => 'Manager User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $manager->syncRoles(['manager']);

        $staff = User::updateOrCreate(
            ['email' => 'staff@test.com'],
            [
                'name' => 'Staff User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $staff->syncRoles(['staff']);

        $this->command->info('Users seeded successfully:');
        $this->command->info('- thanapro70@gmail.com / Tukbuy64@ (Super Admin)');
        $this->command->info('- admin@test.com / password (Admin)');
        $this->command->info('- manager@test.com / password (Manager)');
        $this->command->info('- staff@test.com / password (Staff)');
    }
}
