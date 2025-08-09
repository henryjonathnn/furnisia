<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        \App\Models\User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'role_id' => 1, // admin
                'name' => 'Super Admin',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('Admin_01'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Create Staff
        \App\Models\User::updateOrCreate(
            ['email' => 'jonathan@gmail.com'],
            [
                'role_id' => 2, // staff
                'name' => 'Henry Jonathan',
                'email' => 'jonathan@gmail.com',
                'password' => bcrypt('Staff_01'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        // Create Demo User
        \App\Models\User::updateOrCreate(
            ['email' => 'user@gmail.com'],
            [
                'role_id' => 3, // user
                'name' => 'Demo User',
                'email' => 'user@gmail.com',
                'password' => bcrypt('User_01'),
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );
    }
}
