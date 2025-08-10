<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        // Get popular categories (categories with most products)
        $categories = Category::with('products')
            ->where('is_active', true)
            ->get()
            ->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description,
                    'product_count' => $category->products->where('is_active', true)->count()
                ];
            })
            ->sortByDesc('product_count')
            ->take(6)
            ->values();

        // Get bestseller products (8 products, sorted by highest sold)
        $bestSellers = Product::with('category')
            ->where('is_active', true)
            ->orderBy('sold', 'desc') // Sort by most sold first
            ->take(8)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => 'Rp ' . number_format((float) $product->price, 0, ',', '.'),
                    'image' => $product->image_path,
                    'category' => $product->category?->name,
                    'stock' => $product->stock,
                    'stock_status' => $this->getStockStatus($product->stock),
                    'slug' => \Str::slug($product->name),
                    'sold' => $product->sold
                ];
            });

        // Get limited stock products (8 products, sorted by lowest stock)
        $limitedProducts = Product::with('category')
            ->where('is_active', true)
            ->where('stock', '>', 0) // Only products with stock
            ->orderBy('stock', 'asc') // Lowest stock first
            ->take(8)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => 'Rp ' . number_format((float) $product->price, 0, ',', '.'),
                    'image' => $product->image_path,
                    'category' => $product->category?->name,
                    'stock' => $product->stock,
                    'stock_status' => $this->getStockStatus($product->stock),
                    'slug' => \Str::slug($product->name)
                ];
            });

        return Inertia::render('Customer/Home', [
            'categories' => $categories,
            'bestSellers' => $bestSellers,
            'limitedProducts' => $limitedProducts,
            'stats' => [
                'total_products' => Product::where('is_active', true)->count(),
                'total_categories' => Category::where('is_active', true)->count(),
                'happy_customers' => 1200, // Dummy data
                'years_experience' => 5 // Dummy data
            ]
        ]);
    }

    /**
     * Get stock status based on stock amount
     */
    private function getStockStatus($stock)
    {
        if ($stock == 0) {
            return [
                'label' => 'Habis',
                'color' => 'red',
                'badge_class' => 'bg-red-500/20 text-red-600 border-red-500/30'
            ];
        } elseif ($stock <= 10) {
            return [
                'label' => 'Hampir Habis',
                'color' => 'yellow',
                'badge_class' => 'bg-yellow-500/20 text-yellow-600 border-yellow-500/30'
            ];
        } else {
            return [
                'label' => 'Tersedia',
                'color' => 'green',
                'badge_class' => 'bg-green-500/20 text-green-600 border-green-500/30'
            ];
        }
    }
}
