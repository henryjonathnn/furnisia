<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class OrderController extends Controller
{
    /**
     * Display a listing of orders
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'orderItems.product', 'payment']);

        // Search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('id', 'LIKE', "%{$searchTerm}%")
                  ->orWhereHas('user', function($userQuery) use ($searchTerm) {
                      $userQuery->where('name', 'LIKE', "%{$searchTerm}%")
                               ->orWhere('email', 'LIKE', "%{$searchTerm}%");
                  });
            });
        }

        // Status filter
        if ($request->filled('status_filter')) {
            $query->where('status', $request->status_filter);
        }

        // Payment status filter
        if ($request->filled('payment_filter')) {
            $query->whereHas('payment', function($paymentQuery) use ($request) {
                $paymentQuery->where('status', $request->payment_filter);
            });
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->orderBy($sortField, $sortDirection);

        $orders = $query->paginate(15)->withQueryString();

        // Transform data for frontend
        $orders->getCollection()->transform(function ($order) {
            return [
                'id' => $order->id,
                'order_number' => 'ORD-' . strtoupper(substr($order->id, 0, 8)),
                'user' => [
                    'id' => $order->user->id,
                    'name' => $order->user->name,
                    'email' => $order->user->email,
                ],
                'status' => $order->status,
                'status_label' => $this->getStatusLabel($order->status),
                'status_color' => $this->getStatusColor($order->status),
                'total' => $order->total,
                'formatted_total' => 'Rp ' . number_format((float) $order->total, 0, ',', '.'),
                'items_count' => $order->orderItems->count(),
                'payment_status' => $order->payment?->status ?? 'pending',
                'payment_method' => $order->payment?->method ?? null,
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                'formatted_created_at' => $order->created_at->format('d M Y, H:i'),
                'source' => $this->getOrderSource($order->note),
                'can_update_status' => in_array($order->status, ['pending', 'progress']),
                'can_complete' => $order->status === 'progress' && $order->payment?->status === 'success',
                'is_paid' => $order->payment?->status === 'success',
            ];
        });

        // Calculate statistics
        $stats = [
            'total_orders' => Order::count(),
            'pending_orders' => Order::where('status', 'pending')->count(),
            'progress_orders' => Order::where('status', 'progress')->count(),
            'completed_orders' => Order::where('status', 'completed')->count(),
            'today_orders' => Order::whereDate('created_at', today())->count(),
            'today_revenue' => Order::whereDate('created_at', today())
                                   ->where('status', 'completed')
                                   ->sum('total'),
        ];

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'filters' => $request->only(['search', 'status_filter', 'payment_filter', 'date_from', 'date_to']),
            'stats' => $stats
        ]);
    }

    /**
     * Display the specified order
     */
    public function show($id)
    {
        $order = Order::with(['user', 'orderItems.product.category', 'payment'])
            ->findOrFail($id);

        $orderData = [
            'id' => $order->id,
            'order_number' => 'ORD-' . strtoupper(substr($order->id, 0, 8)),
            'user' => [
                'id' => $order->user->id,
                'name' => $order->user->name,
                'email' => $order->user->email,
            ],
            'status' => $order->status,
            'status_label' => $this->getStatusLabel($order->status),
            'status_color' => $this->getStatusColor($order->status),
            'total' => $order->total,
            'formatted_total' => 'Rp ' . number_format($order->total, 0, ',', '.'),
            'created_at' => $order->created_at->format('Y-m-d H:i:s'),
            'formatted_created_at' => $order->created_at->format('d M Y, H:i'),
            'items' => $order->orderItems->map(function ($item) {
                return [
                    'id' => $item->id,
                    'product' => [
                        'id' => $item->product->id,
                        'name' => $item->product->name,
                        'image' => $item->product->image_path,
                        'category' => $item->product->category?->name,
                        'price' => $item->product->price,
                        'formatted_price' => 'Rp ' . number_format($item->product->price, 0, ',', '.')
                    ],
                    'quantity' => $item->quantity,
                    'subtotal' => $item->subtotal,
                    'formatted_subtotal' => 'Rp ' . number_format($item->subtotal, 0, ',', '.')
                ];
            }),
            'payment' => $order->payment ? [
                'id' => $order->payment->id,
                'method' => $order->payment->method,
                'status' => $order->payment->status,
                'paid_at' => $order->payment->paid_at?->format('d M Y, H:i')
            ] : null,
            'source' => $this->getOrderSource($order->note),
        ];

        return response()->json($orderData);
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,progress,completed,cancelled'
        ]);

        $order = Order::findOrFail($id);
        
        $oldStatus = $order->status;
        $order->update(['status' => $request->status]);

        // Log the status change
        Log::info('Order status updated', [
            'order_id' => $order->id,
            'old_status' => $oldStatus,
            'new_status' => $request->status,
            'updated_by' => auth()->id()
        ]);

        return back()->with('success', 'Status pesanan berhasil diperbarui');
    }

    /**
     * Complete order (finish processing)
     */
    public function completeOrder($id)
    {
        $order = Order::findOrFail($id);
        
        // Check if order can be completed
        if ($order->status !== 'progress') {
            return response()->json([
                'success' => false,
                'message' => 'Hanya pesanan yang sedang diproses yang bisa diselesaikan'
            ], 400);
        }

        // Update status to completed
        $order->update(['status' => 'completed']);

        // Log the completion
        Log::info('Order completed by admin', [
            'order_id' => $order->id,
            'completed_by' => auth()->id(),
            'completed_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pesanan berhasil diselesaikan'
        ]);
    }

    /**
     * Get order details for viewing
     */
    public function details($id)
    {
        $order = Order::with(['user', 'orderItems.product.category', 'payment'])
            ->findOrFail($id);

        $orderData = [
            'order' => [
                'id' => $order->id,
                'order_number' => 'ORD-' . strtoupper(substr($order->id, 0, 8)),
                'user' => [
                    'id' => $order->user->id,
                    'name' => $order->user->name,
                    'email' => $order->user->email,
                ],
                'status' => $order->status,
                'status_label' => $this->getStatusLabel($order->status),
                'status_color' => $this->getStatusColor($order->status),
                'total' => $order->total,
                'formatted_total' => 'Rp ' . number_format((float) $order->total, 0, ',', '.'),
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
                'formatted_created_at' => $order->created_at->format('d M Y, H:i'),
                'items' => $order->orderItems->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'product' => [
                            'id' => $item->product->id,
                            'name' => $item->product->name,
                            'image' => $item->product->image_path,
                            'category' => $item->product->category?->name,
                            'price' => $item->product->price,
                            'formatted_price' => 'Rp ' . number_format($item->product->price, 0, ',', '.')
                        ],
                        'quantity' => $item->quantity,
                        'subtotal' => $item->subtotal,
                        'formatted_subtotal' => 'Rp ' . number_format($item->subtotal, 0, ',', '.')
                    ];
                }),
                'payment' => $order->payment ? [
                    'id' => $order->payment->id,
                    'method' => $order->payment->method,
                    'status' => $order->payment->status,
                    'paid_at' => $order->payment->paid_at?->format('d M Y, H:i')
                ] : null,
                'source' => $this->getOrderSource($order->note),
            ],
            'timeline' => [
                [
                    'status' => 'pending',
                    'label' => 'Pesanan Dibuat',
                    'date' => $order->created_at->format('d M Y, H:i'),
                    'active' => true
                ],
                [
                    'status' => 'progress', 
                    'label' => 'Sedang Diproses',
                    'date' => $order->status === 'progress' ? 'Sedang berlangsung' : null,
                    'active' => in_array($order->status, ['progress', 'completed'])
                ],
                [
                    'status' => 'completed',
                    'label' => 'Selesai',
                    'date' => $order->status === 'completed' ? 'Pesanan selesai' : null,
                    'active' => $order->status === 'completed'
                ]
            ]
        ];

        return response()->json($orderData);
    }

    /**
     * Get status label
     */
    private function getStatusLabel($status)
    {
        $labels = [
            'pending' => 'Menunggu Pembayaran',
            'progress' => 'Sedang Diproses',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan'
        ];

        return $labels[$status] ?? $status;
    }

    /**
     * Get status color
     */
    private function getStatusColor($status)
    {
        $colors = [
            'pending' => 'yellow',
            'progress' => 'blue',
            'completed' => 'green',
            'cancelled' => 'red'
        ];

        return $colors[$status] ?? 'gray';
    }

    /**
     * Get order source from note
     */
    private function getOrderSource($note)
    {
        if (!$note) return 'Unknown';
        
        $noteData = json_decode($note, true);
        
        if (isset($noteData['source'])) {
            return $noteData['source'] === 'buy_now' ? 'Beli Sekarang' : 
                   ($noteData['source'] === 'cart_checkout' ? 'Keranjang' : 'Unknown');
        }
        
        return 'Unknown';
    }
}