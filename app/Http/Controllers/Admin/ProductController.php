<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProductController extends Controller
{
    /**
     * Display a listing of products with filters and pagination
     */
    public function index(Request $request)
    {
        $query = Product::with('category')
            ->withTrashed($request->boolean('include_deleted', false));

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category_filter')) {
            $query->where('category_id', $request->input('category_filter'));
        }

        // Status filter
        if ($request->filled('status_filter')) {
            $query->where('is_active', $request->boolean('status_filter'));
        }

        // Stock filter
        if ($request->filled('stock_filter')) {
            $stockFilter = $request->input('stock_filter');
            switch ($stockFilter) {
                case 'low':
                    $query->where('stock', '<=', 10);
                    break;
                case 'out':
                    $query->where('stock', 0);
                    break;
                case 'available':
                    $query->where('stock', '>', 0);
                    break;
            }
        }

        // Price range filter
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->input('min_price'));
        }
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->input('max_price'));
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        
        $allowedSorts = ['name', 'price', 'stock', 'created_at', 'updated_at'];
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortDirection);
        }

        // Paginate results
        $products = $query->paginate(15)->withQueryString();

        // Transform data for frontend
        $products->through(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => Str::limit($product->description, 100),
                'price' => $product->price,
                'formatted_price' => $product->formatted_price,
                'stock' => $product->stock,
                'category' => $product->category ? [
                    'id' => $product->category->id,
                    'name' => $product->category->name
                ] : null,
                'is_active' => $product->is_active,
                'image_path' => $product->image_path,
                'created_at' => $product->created_at?->format('Y-m-d H:i:s') ?? 'N/A',
                'updated_at' => $product->updated_at?->format('Y-m-d H:i:s') ?? 'N/A',
                'deleted_at' => $product->deleted_at?->format('Y-m-d H:i:s'),
                'stock_status' => $this->getStockStatus($product->stock),
            ];
        });

        // Get active categories for filter dropdown
        $categories = Category::where('is_active', true)->get(['id', 'name']);

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'categories' => $categories,
            'filters' => $request->only([
                'search', 'category_filter', 'status_filter', 'stock_filter',
                'min_price', 'max_price', 'sort_by', 'sort_direction', 'include_deleted'
            ]),
            'stats' => [
                'total_products' => Product::count(),
                'active_products' => Product::where('is_active', true)->count(),
                'low_stock_products' => Product::where('stock', '<=', 10)->where('stock', '>', 0)->count(),
                'out_of_stock_products' => Product::where('stock', 0)->count(),
            ]
        ]);
    }

    /**
     * Show the form for creating a new product
     */
    public function create()
    {
        $categories = Category::where('is_active', true)->get(['id', 'name']);
        
        return Inertia::render('Admin/Products/Create', [
            'categories' => $categories
        ]);
    }

    /**
     * Store a newly created product in storage
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191|unique:products,name',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'is_active' => 'boolean',
            'specs' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image_path'] = $imagePath;
        }

        $product = Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan.');
    }

    /**
     * Display the specified product
     */
    public function show(Product $product)
    {
        $product->load('category');
        
        // Get product statistics
        $stats = [
            'total_orders' => $product->orderItems()->count(),
            'total_sold' => $product->orderItems()->sum('quantity'),
            'total_revenue' => $product->orderItems()->sum('subtotal'),
            'in_carts' => $product->carts()->count(),
            'avg_order_quantity' => $product->orderItems()->avg('quantity') ?? 0,
        ];

        return Inertia::render('Admin/Products/Show', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'formatted_price' => $product->formatted_price,
                'stock' => $product->stock,
                'category' => $product->category,
                'is_active' => $product->is_active,
                'image_path' => $product->image_path,
                'specs' => $product->specs,
                'created_at' => $product->created_at?->format('Y-m-d H:i:s') ?? 'N/A',
                'updated_at' => $product->updated_at?->format('Y-m-d H:i:s') ?? 'N/A',
                'stock_status' => $this->getStockStatus($product->stock),
            ],
            'stats' => $stats
        ]);
    }

    /**
     * Show the form for editing the specified product
     */
    public function edit(Product $product)
    {
        $categories = Category::where('is_active', true)->get(['id', 'name']);
        
        return Inertia::render('Admin/Products/Edit', [
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'stock' => $product->stock,
                'category_id' => $product->category_id,
                'is_active' => $product->is_active,
                'image_path' => $product->image_path,
                'specs' => $product->specs,
            ],
            'categories' => $categories
        ]);
    }

    /**
     * Update the specified product in storage
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:191|unique:products,name,' . $product->id,
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'is_active' => 'boolean',
            'specs' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $validated['is_active'] = $request->boolean('is_active', true);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            
            $imagePath = $request->file('image')->store('products', 'public');
            $validated['image_path'] = $imagePath;
        }

        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diperbarui.');
    }

    /**
     * Remove the specified product from storage
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * Restore the specified product
     */
    public function restore($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->restore();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dipulihkan.');
    }

    /**
     * Force delete the specified product
     */
    public function forceDelete($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        
        // Delete image file
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        
        $product->forceDelete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus permanen.');
    }

    /**
     * Update product stock
     */
    public function updateStock(Request $request, Product $product)
    {
        $validated = $request->validate([
            'stock' => 'required|integer|min:0',
            'reason' => 'nullable|string|max:255'
        ]);

        $oldStock = $product->stock;
        $product->update(['stock' => $validated['stock']]);

        // Log stock change (you might want to create a StockHistory model)
        // StockHistory::create([
        //     'product_id' => $product->id,
        //     'old_stock' => $oldStock,
        //     'new_stock' => $validated['stock'],
        //     'reason' => $validated['reason'],
        //     'user_id' => auth()->id(),
        // ]);

        return response()->json([
            'success' => true,
            'message' => 'Stok produk berhasil diperbarui.',
            'data' => [
                'old_stock' => $oldStock,
                'new_stock' => $product->stock,
                'difference' => $product->stock - $oldStock
            ]
        ]);
    }

    /**
     * Get product details for API/AJAX requests
     */
    public function details(Product $product)
    {
        $product->load('category');
        
        $stats = [
            'total_orders' => $product->orderItems()->count(),
            'total_sold' => $product->orderItems()->sum('quantity'),
            'total_revenue' => $product->orderItems()->sum('subtotal'),
            'in_carts' => $product->carts()->count(),
            'avg_order_quantity' => round($product->orderItems()->avg('quantity') ?? 0, 2),
            'last_sold' => $product->orderItems()->latest()->first()?->created_at?->format('Y-m-d H:i:s'),
        ];

        return response()->json([
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'formatted_price' => $product->formatted_price,
                'stock' => $product->stock,
                'category' => $product->category,
                'is_active' => $product->is_active,
                'image_path' => $product->image_path,
                'specs' => $product->specs,
                'created_at' => $product->created_at?->format('Y-m-d H:i:s') ?? 'N/A',
                'updated_at' => $product->updated_at?->format('Y-m-d H:i:s') ?? 'N/A',
                'stock_status' => $this->getStockStatus($product->stock),
            ],
            'stats' => $stats
        ]);
    }

    /**
     * Get stock status label and color
     */
    private function getStockStatus($stock)
    {
        if ($stock == 0) {
            return ['label' => 'Out of Stock', 'color' => 'red'];
        } elseif ($stock <= 10) {
            return ['label' => 'Low Stock', 'color' => 'yellow'];
        } else {
            return ['label' => 'In Stock', 'color' => 'green'];
        }
    }
}