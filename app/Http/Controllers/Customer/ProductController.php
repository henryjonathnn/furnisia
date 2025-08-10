<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display all products with filtering
     */
    public function index(Request $request)
    {
        $query = Product::with('category')->where('is_active', true);
        
        // Initialize filter info
        $filterInfo = [
            'type' => 'semua produk',
            'value' => '',
            'description' => 'Menampilkan semua produk yang tersedia'
        ];

        // Handle category filter
        if ($request->has('category')) {
            $categorySlug = $request->get('category');
            $category = Category::where('slug', $categorySlug)->first();
            
            if ($category) {
                $query->where('category_id', $category->id);
                $filterInfo = [
                    'type' => 'kategori',
                    'value' => $category->name,
                    'description' => "Produk dalam kategori {$category->name}"
                ];
            }
        }

        // Handle search filter
        if ($request->has('search') && $request->get('search')) {
            $searchTerm = $request->get('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                  ->orWhereHas('category', function($categoryQuery) use ($searchTerm) {
                      $categoryQuery->where('name', 'LIKE', "%{$searchTerm}%");
                  });
            });
            
            $filterInfo = [
                'type' => 'pencarian',
                'value' => $searchTerm,
                'description' => "Hasil pencarian untuk \"{$searchTerm}\""
            ];
        }

        // Handle price range filter
        if ($request->has('min_price') && $request->get('min_price')) {
            $query->where('price', '>=', $request->get('min_price'));
        }
        
        if ($request->has('max_price') && $request->get('max_price')) {
            $query->where('price', '<=', $request->get('max_price'));
        }

        // Handle sorting
        $sort = $request->get('sort', 'latest');
        switch ($sort) {
            case 'price_low':
                $query->orderBy('price', 'asc');
                break;
            case 'price_high':
                $query->orderBy('price', 'desc');
                break;
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'sold_high':
                $query->orderBy('sold', 'desc');
                break;
            case 'sold_low':
                $query->orderBy('sold', 'asc');
                break;
            case 'stock_high':
                $query->orderBy('stock', 'desc');
                break;
            case 'stock_low':
                $query->orderBy('stock', 'asc');
                break;
            case 'latest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        // Get products with pagination
        $products = $query->paginate(20)->withQueryString();
        
        // Transform products
        $products->getCollection()->transform(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => 'Rp ' . number_format((float) $product->price, 0, ',', '.'),
                'raw_price' => (float) $product->price,
                'image' => $product->image_path,
                'category' => $product->category?->name,
                'stock' => $product->stock,
                'sold' => $product->sold,
                'stock_status' => $this->getStockStatus($product->stock),
                'slug' => \Str::slug($product->name),
                'created_at' => $product->created_at->format('d M Y')
            ];
        });

        // Get all categories for filter
        $categories = Category::where('is_active', true)
            ->withCount(['products' => function($query) {
                $query->where('is_active', true);
            }])
            ->orderBy('name')
            ->get()
            ->map(function($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'product_count' => $category->products_count
                ];
            });

        // Get price range for filter
        $priceRange = Product::where('is_active', true)->selectRaw('MIN(price) as min_price, MAX(price) as max_price')->first();

        return Inertia::render('Customer/Products', [
            'products' => $products,
            'filterInfo' => $filterInfo,
            'categories' => $categories,
            'priceRange' => [
                'min' => (float) $priceRange->min_price,
                'max' => (float) $priceRange->max_price
            ],
            'currentFilters' => [
                'category' => $request->get('category'),
                'search' => $request->get('search'),
                'min_price' => $request->get('min_price'),
                'max_price' => $request->get('max_price'),
                'sort' => $request->get('sort', 'latest')
            ]
        ]);
    }

    /**
     * Display the specified product
     */
    public function show($slug)
    {
        // Find product by slug (we'll use name slug for now)
        $product = Product::with('category')
            ->where('is_active', true)
            ->whereRaw('LOWER(REPLACE(name, " ", "-")) = ?', [strtolower($slug)])
            ->firstOrFail();

        // Get related products (same category, excluding current product)
        $relatedProducts = Product::with('category')
            ->where('is_active', true)
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get()
            ->map(function ($relatedProduct) {
                return [
                    'id' => $relatedProduct->id,
                    'name' => $relatedProduct->name,
                    'formatted_price' => 'Rp ' . number_format($relatedProduct->price, 0, ',', '.'),
                    'image_path' => $relatedProduct->image_path,
                    'category' => $relatedProduct->category ? [
                        'id' => $relatedProduct->category->id,
                        'name' => $relatedProduct->category->name
                    ] : null,
                    'stock_status' => $this->getStockStatus($relatedProduct->stock),
                    'slug' => \Str::slug($relatedProduct->name)
                ];
            });

        return Inertia::render('Customer/ProductDetail', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'formatted_price' => 'Rp ' . number_format($product->price, 0, ',', '.'),
                'stock' => $product->stock,
                'image_path' => $product->image_path,
                'specs' => $product->specs,
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'name' => $product->category->name
                ] : null,
                'stock_status' => $this->getStockStatus($product->stock),
                'slug' => \Str::slug($product->name)
            ],
            'relatedProducts' => $relatedProducts
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

    /**
     * Search suggestions API
     */
    public function suggestions(Request $request)
    {
        $searchTerm = $request->get('q');
        
        if (!$searchTerm || strlen($searchTerm) < 2) {
            return response()->json([]);
        }

        $products = Product::with('category')
            ->where('is_active', true)
            ->where(function($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%")
                  ->orWhereHas('category', function($categoryQuery) use ($searchTerm) {
                      $categoryQuery->where('name', 'LIKE', "%{$searchTerm}%");
                  });
            })
            ->limit(8)
            ->get()
            ->map(function($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'image' => $product->image_path,
                    'category' => $product->category?->name,
                    'price' => 'Rp ' . number_format((float) $product->price, 0, ',', '.'),
                    'slug' => \Str::slug($product->name)
                ];
            });

        return response()->json($products);
    }
}