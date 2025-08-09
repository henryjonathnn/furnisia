<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WalletSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create main treasury wallet
        \App\Models\Wallet::updateOrCreate(
            ['id' => 1],
            [
                'balance' => 0, // Start with 0 balance
            ]
        );
    }
}
