<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Pastikan hanya admin/staff yang bisa akses
        $user = auth()->user();
        
        if (!$user->isAdmin() && !$user->isStaff()) {
            abort(404, 'Page not found'); // 404 instead of 403 for users
        }

        // Get dashboard statistics
        $stats = $this->getDashboardStats();
        $recentOrders = $this->getRecentOrders();
        $topProducts = $this->getTopProducts();
        $lowStockProducts = $this->getLowStockProducts();
        $alerts = $this->getAlerts();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
            'topProducts' => $topProducts,
            'lowStockProducts' => $lowStockProducts,
            'alerts' => $alerts,
            'user' => auth()->user()->load('role')
        ]);
    }

    private function getDashboardStats()
    {
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        $thisMonth = Carbon::now()->startOfMonth();
        $lastMonth = Carbon::now()->subMonth()->startOfMonth();

        // Total Sales (dari wallet balance)
        $totalSales = Wallet::first()->balance ?? 0;
        
        // Sales comparison (today vs yesterday)
        $todaySales = WalletTransaction::whereDate('created_at', $today)->sum('amount');
        $yesterdaySales = WalletTransaction::whereDate('created_at', $yesterday)->sum('amount');
        $salesGrowth = $yesterdaySales > 0 ? (($todaySales - $yesterdaySales) / $yesterdaySales) * 100 : 0;

        // New Orders (pending orders count)
        $newOrders = Order::where('status', 'pending')->count();
        $totalOrdersToday = Order::whereDate('created_at', $today)->count();
        
        // Low Stock Products
        $lowStockCount = Product::where('stock', '<', 5)
            ->where('is_active', true)
            ->count();

        // Store Rating (dummy for now - could be calculated from reviews)
        $storeRating = 4.9;

        // Recent orders growth
        $recentOrdersGrowth = Order::whereDate('created_at', $today)->count();

        return [
            'totalSales' => [
                'value' => $totalSales,
                'formatted' => 'Rp ' . number_format($totalSales, 0, ',', '.'),
                'growth' => round($salesGrowth, 1),
                'growthFormatted' => ($salesGrowth >= 0 ? '+' : '') . round($salesGrowth, 1) . '%'
            ],
            'newOrders' => [
                'value' => $newOrders,
                'growth' => $recentOrdersGrowth,
                'growthFormatted' => '+' . $recentOrdersGrowth . ' pesanan'
            ],
            'lowStock' => [
                'value' => $lowStockCount,
                'status' => $lowStockCount > 0 ? 'warning' : 'good'
            ],
            'storeRating' => [
                'value' => $storeRating,
                'status' => 'excellent'
            ]
        ];
    }

    private function getRecentOrders()
    {
        return Order::with(['user', 'orderItems.product'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($order) {
                $firstProduct = $order->orderItems->first()?->product;
                
                return [
                    'id' => $order->id,
                    'order_number' => '#' . str_pad($order->id, 5, '0', STR_PAD_LEFT),
                    'customer_name' => $order->user->name,
                    'product_name' => $firstProduct ? $firstProduct->name : 'Unknown Product',
                    'total' => 'Rp ' . number_format($order->total, 0, ',', '.'),
                    'status' => $order->status,
                    'status_label' => $this->getStatusLabel($order->status),
                    'status_color' => $this->getStatusColor($order->status),
                    'created_at' => $order->created_at->diffForHumans(),
                    'product_image' => $firstProduct?->image_path
                ];
            });
    }

    private function getTopProducts()
    {
        return Product::select([
                'products.id',
                'products.name', 
                'products.price',
                'products.image_path',
                'products.created_at',
                'products.updated_at'
            ])
            ->selectRaw('COALESCE(SUM(order_items.quantity), 0) as total_sold')
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('orders', function($join) {
                $join->on('order_items.order_id', '=', 'orders.id')
                     ->where('orders.status', '!=', 'cancelled');
            })
            ->where('products.is_active', true)
            ->groupBy([
                'products.id',
                'products.name', 
                'products.price',
                'products.image_path',
                'products.created_at',
                'products.updated_at'
            ])
            ->orderByDesc('total_sold')
            ->take(3)
            ->get()
            ->map(function ($product, $index) {
                return [
                    'rank' => $index + 1,
                    'name' => $product->name,
                    'total_sold' => $product->total_sold,
                    'price' => 'Rp ' . number_format($product->price, 0, ',', '.'),
                    'image' => $product->image_path,
                    'growth' => rand(15, 35) // Dummy growth percentage
                ];
            });
    }

    private function getLowStockProducts()
    {
        return Product::where('stock', '<', 5)
            ->where('is_active', true)
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get()
            ->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'stock' => $product->stock,
                    'price' => 'Rp ' . number_format($product->price, 0, ',', '.'),
                    'image' => $product->image_path
                ];
            });
    }

    private function getAlerts()
    {
        $alerts = [];

        // Low Stock Alert
        $lowStockCount = Product::where('stock', '<', 5)
            ->where('is_active', true)
            ->count();
        
        if ($lowStockCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'fas fa-exclamation-triangle',
                'title' => 'Stok Kritis',
                'message' => $lowStockCount . ' produk dengan stok < 5 unit',
                'color' => 'red'
            ];
        }

        // Pending Orders Alert
        $pendingOrdersCount = Order::where('status', 'pending')->count();
        
        if ($pendingOrdersCount > 0) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'fas fa-clock',
                'title' => 'Pesanan Tertunda',
                'message' => $pendingOrdersCount . ' pesanan menunggu konfirmasi',
                'color' => 'orange'
            ];
        }

        return $alerts;
    }

    private function getStatusLabel($status)
    {
        return match($status) {
            'pending' => 'Menunggu',
            'progress' => 'Proses',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($status)
        };
    }

    private function getStatusColor($status)
    {
        return match($status) {
            'pending' => 'yellow',
            'progress' => 'blue',
            'completed' => 'green',
            'cancelled' => 'red',
            default => 'gray'
        };
    }


}
