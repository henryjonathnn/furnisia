<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,       // 1. Roles first (admin, staff, user)
            UserSeeder::class,       // 2. Users (need roles to exist)
            CategorySeeder::class,   // 3. Categories for products
            ProductSeeder::class,    // 4. Sample products
            WalletSeeder::class,     // 5. Treasury wallet
        ]);
        
        $this->command->info('🎉 Seeding completed! Login credentials:');
        $this->command->info('Admin: admin@gmail.com / Admin_01');
        $this->command->info('Staff: jonathan@gmail.com / Staff_01');
        $this->command->info('User: user1@gmail.com / User_01');
    }
}
