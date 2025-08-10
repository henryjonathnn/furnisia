<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Get categories or create if not exist
        $categories = [
            'Perabotan' => 'Furniture untuk rumah dan kantor',
            'Elektronik Rumah' => 'Peralatan elektronik untuk rumah',
            'Peralatan Dapur' => 'Perlengkapan memasak dan dapur',
            'Peralatan Mandi' => 'Perlengkapan kamar mandi',
            'Perlengkapan Tidur' => 'Kasur, bantal, dan perlengkapan tidur',
            'Alat Kebersihan' => 'Peralatan untuk membersihkan rumah'
        ];

        $categoryModels = [];
        foreach ($categories as $name => $description) {
            $categoryModels[] = Category::firstOrCreate(
                ['name' => $name],
                [
                    'slug' => Str::slug($name),
                    'description' => $description,
                    'is_active' => true
                ]
            );
        }

        // Sample products data
        $products = [
            // Perabotan
            [
                'name' => 'Sofa Minimalis 3 Dudukan',
                'description' => 'Sofa minimalis dengan desain modern untuk ruang tamu. Terbuat dari bahan berkualitas tinggi dengan busa yang empuk dan nyaman. Cocok untuk dekorasi rumah minimalis.',
                'price' => 2500000,
                'stock' => 15,
                'category' => 'Perabotan',
                'specs' => [
                    'Material' => 'Fabric dan Kayu Jati',
                    'Dimensi' => '180 x 80 x 85 cm',
                    'Warna' => 'Abu-abu',
                    'Kapasitas' => '3 Orang'
                ]
            ],
            [
                'name' => 'Meja Makan Kayu Jati',
                'description' => 'Meja makan solid dari kayu jati asli dengan finishing natural. Desain klasik yang timeless dan tahan lama. Ideal untuk keluarga kecil hingga sedang.',
                'price' => 3200000,
                'stock' => 8,
                'category' => 'Perabotan',
                'specs' => [
                    'Material' => 'Kayu Jati Solid',
                    'Dimensi' => '150 x 90 x 75 cm',
                    'Finishing' => 'Natural Wood',
                    'Kapasitas' => '6 Orang'
                ]
            ],
            [
                'name' => 'Lemari Pakaian 3 Pintu',
                'description' => 'Lemari pakaian dengan 3 pintu dan cermin. Dilengkapi dengan gantungan baju, rak, dan laci. Desain modern dengan space yang optimal.',
                'price' => 1800000,
                'stock' => 12,
                'category' => 'Perabotan',
                'specs' => [
                    'Material' => 'MDF dan Kaca',
                    'Dimensi' => '120 x 50 x 200 cm',
                    'Fitur' => 'Cermin, Gantungan, Rak',
                    'Pintu' => '3 Pintu'
                ]
            ],

            // Elektronik Rumah
            [
                'name' => 'Smart TV LED 43 Inch',
                'description' => 'Smart TV LED dengan resolusi 4K Ultra HD. Dilengkapi dengan fitur smart TV, WiFi, dan berbagai aplikasi streaming. Suara jernih dengan teknologi Dolby Audio.',
                'price' => 4500000,
                'stock' => 25,
                'category' => 'Elektronik Rumah',
                'specs' => [
                    'Ukuran' => '43 Inch',
                    'Resolusi' => '4K Ultra HD',
                    'Smart TV' => 'Yes',
                    'Konektivitas' => 'WiFi, HDMI, USB'
                ]
            ],
            [
                'name' => 'AC Split 1 PK',
                'description' => 'Air conditioner split dengan kapasitas 1 PK. Hemat energi dengan teknologi inverter. Dilengkapi remote control dan timer. Cocok untuk ruangan 12-15 m².',
                'price' => 3800000,
                'stock' => 18,
                'category' => 'Elektronik Rumah',
                'specs' => [
                    'Kapasitas' => '1 PK',
                    'Teknologi' => 'Inverter',
                    'Coverage' => '12-15 m²',
                    'Fitur' => 'Remote, Timer, Auto Clean'
                ]
            ],

            // Peralatan Dapur
            [
                'name' => 'Rice Cooker Digital 1.8L',
                'description' => 'Rice cooker digital dengan kapasitas 1.8 liter. Dilengkapi dengan berbagai menu masak, timer, dan keep warm function. Non-stick coating untuk mudah dibersihkan.',
                'price' => 850000,
                'stock' => 30,
                'category' => 'Peralatan Dapur',
                'specs' => [
                    'Kapasitas' => '1.8 Liter',
                    'Tipe' => 'Digital',
                    'Fitur' => 'Timer, Keep Warm, Multi Menu',
                    'Coating' => 'Non-stick'
                ]
            ],
            [
                'name' => 'Blender Multifungsi 2L',
                'description' => 'Blender multifungsi dengan kapasitas 2 liter. Motor yang kuat untuk menghancurkan es dan buah keras. Dilengkapi dengan berbagai mata pisau.',
                'price' => 650000,
                'stock' => 22,
                'category' => 'Peralatan Dapur',
                'specs' => [
                    'Kapasitas' => '2 Liter',
                    'Power' => '500 Watt',
                    'Fitur' => 'Ice Crush, Pulse',
                    'Material' => 'Stainless Steel Blade'
                ]
            ],

            // Peralatan Mandi
            [
                'name' => 'Shower Set Premium',
                'description' => 'Set shower premium dengan hand shower dan rain shower. Terbuat dari stainless steel berkualitas tinggi. Dilengkapi dengan flexible hose 1.5 meter.',
                'price' => 1200000,
                'stock' => 15,
                'category' => 'Peralatan Mandi',
                'specs' => [
                    'Material' => 'Stainless Steel',
                    'Tipe' => 'Hand + Rain Shower',
                    'Hose' => '1.5 meter',
                    'Finish' => 'Chrome'
                ]
            ],

            // Perlengkapan Tidur
            [
                'name' => 'Kasur Spring Bed Queen Size',
                'description' => 'Kasur spring bed dengan ukuran queen size. Dilengkapi dengan pocket spring untuk kenyamanan maksimal. Top layer memory foam untuk support yang optimal.',
                'price' => 5500000,
                'stock' => 10,
                'category' => 'Perlengkapan Tidur',
                'specs' => [
                    'Ukuran' => 'Queen Size (160x200)',
                    'Tipe' => 'Pocket Spring',
                    'Top Layer' => 'Memory Foam',
                    'Ketebalan' => '25 cm'
                ]
            ],
            [
                'name' => 'Set Sprei Katun Premium',
                'description' => 'Set sprei dari katun premium dengan motif elegan. Terdiri dari sprei, sarung bantal, dan sarung guling. Mudah dicuci dan tahan lama.',
                'price' => 350000,
                'stock' => 45,
                'category' => 'Perlengkapan Tidur',
                'specs' => [
                    'Material' => 'Katun Premium',
                    'Ukuran' => 'Queen Size',
                    'Isi Set' => 'Sprei + 2 Sarung Bantal + Sarung Guling',
                    'Motif' => 'Floral Modern'
                ]
            ],

            // Alat Kebersihan
            [
                'name' => 'Vacuum Cleaner 2 in 1',
                'description' => 'Vacuum cleaner 2 in 1 yang bisa digunakan sebagai stick vacuum dan handheld. Cordless dengan battery lithium yang tahan lama. Dilengkapi berbagai nozzle.',
                'price' => 1500000,
                'stock' => 20,
                'category' => 'Alat Kebersihan',
                'specs' => [
                    'Tipe' => '2 in 1 (Stick + Handheld)',
                    'Power' => 'Cordless Battery',
                    'Battery' => 'Lithium 2200mAh',
                    'Runtime' => '35 menit'
                ]
            ]
        ];

        foreach ($products as $productData) {
            $category = Category::where('name', $productData['category'])->first();
            
            Product::create([
                'category_id' => $category->id,
                'name' => $productData['name'],
                'description' => $productData['description'],
                'price' => $productData['price'],
                'stock' => $productData['stock'],
                'sold' => rand(10, 100), // Random sold count untuk testing
                'is_active' => true,
                'specs' => $productData['specs']
            ]);
        }

        // Create some products with low stock for testing
        $lowStockProducts = [
            [
                'name' => 'Lampu Hias LED Strip',
                'description' => 'LED strip untuk dekorasi ruangan dengan berbagai warna.',
                'price' => 250000,
                'stock' => 3,
                'category' => 'Elektronik Rumah'
            ],
            [
                'name' => 'Gelas Set Minimalis',
                'description' => 'Set gelas minimalis untuk kebutuhan sehari-hari.',
                'price' => 150000,
                'stock' => 5,
                'category' => 'Peralatan Dapur'
            ]
        ];

        foreach ($lowStockProducts as $productData) {
            $category = Category::where('name', $productData['category'])->first();
            
            Product::create([
                'category_id' => $category->id,
                'name' => $productData['name'],
                'description' => $productData['description'],
                'price' => $productData['price'],
                'stock' => $productData['stock'],
                'sold' => rand(1, 15), // Random sold count untuk testing
                'is_active' => true,
                'specs' => []
            ]);
        }

        // Create one out of stock product for testing
        $category = Category::where('name', 'Perabotan')->first();
        Product::create([
            'category_id' => $category->id,
            'name' => 'Kursi Gaming Pro',
            'description' => 'Kursi gaming ergonomis dengan fitur reclining dan lumbar support.',
            'price' => 2200000,
            'stock' => 0,
            'sold' => 25, // Sold out product tapi ada history penjualan
            'is_active' => true,
            'specs' => [
                'Material' => 'PU Leather',
                'Fitur' => 'Reclining, Lumbar Support',
                'Max Weight' => '120 kg',
                'Adjustable' => 'Height, Armrest'
            ]
        ]);
    }
}
