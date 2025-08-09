<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Peralatan Dapur',
                'slug' => 'peralatan-dapur',
                'description' => 'Peralatan masak dan dapur untuk kebutuhan sehari-hari',
                'is_active' => true,
            ],
            [
                'name' => 'Peralatan Mandi',
                'slug' => 'peralatan-mandi',
                'description' => 'Perlengkapan mandi dan kebersihan pribadi',
                'is_active' => true,
            ],
            [
                'name' => 'Alat Kebersihan',
                'slug' => 'alat-kebersihan',
                'description' => 'Peralatan untuk membersihkan rumah',
                'is_active' => true,
            ],
            [
                'name' => 'Elektronik Rumah',
                'slug' => 'elektronik-rumah',
                'description' => 'Peralatan elektronik untuk rumah tangga',
                'is_active' => true,
            ],
            [
                'name' => 'Perabotan',
                'slug' => 'perabotan',
                'description' => 'Furniture dan perabotan rumah tangga',
                'is_active' => true,
            ],
            [
                'name' => 'Perlengkapan Tidur',
                'slug' => 'perlengkapan-tidur',
                'description' => 'Kasur, bantal, sprei dan perlengkapan kamar tidur',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            );
        }
    }
}
