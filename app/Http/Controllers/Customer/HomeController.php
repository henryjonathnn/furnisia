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
        // Get featured products (latest 8 products)
        $featuredProducts = Product::with('category')
            ->where('is_active', true)
            ->latest()
            ->take(8)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => 'Rp ' . number_format($product->price, 0, ',', '.'),
                    'image' => $product->image_path,
                    'category' => $product->category?->name,
                    'in_stock' => $product->stock > 0,
                    'stock' => $product->stock
                ];
            });

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

        // Get bestseller products (if we had sales data)
        $bestSellers = Product::with('category')
            ->where('is_active', true)
            ->where('stock', '>', 0)
            ->inRandomOrder() // Temporary random until we have real sales data
            ->take(4)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => 'Rp ' . number_format($product->price, 0, ',', '.'),
                    'image' => $product->image_path,
                    'category' => $product->category?->name,
                    'rating' => 4.5 + (rand(1, 10) / 10), // Dummy rating
                    'sold' => rand(50, 200) // Dummy sold count
                ];
            });

        return Inertia::render('Customer/Home', [
            'featuredProducts' => $featuredProducts,
            'categories' => $categories,
            'bestSellers' => $bestSellers,
            'stats' => [
                'total_products' => Product::where('is_active', true)->count(),
                'total_categories' => Category::where('is_active', true)->count(),
                'happy_customers' => 1200, // Dummy data
                'years_experience' => 5 // Dummy data
            ]
        ]);
    }
}
