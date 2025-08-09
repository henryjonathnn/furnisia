<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['id' => 1, 'name' => 'admin'],
            ['id' => 2, 'name' => 'staff'],
            ['id' => 3, 'name' => 'user'],
        ];

        foreach ($roles as $role) {
            \App\Models\Role::updateOrCreate(
                ['id' => $role['id']], 
                $role
            );
        }
    }
}
