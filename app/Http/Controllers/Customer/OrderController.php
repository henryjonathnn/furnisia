<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderController extends Controller
{
    /**
     * Display user's orders
     */
    public function index()
    {
        $orders = Order::with(['orderItems.product.category', 'payment'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'order_number' => 'ORD-' . strtoupper(substr($order->id, 0, 8)),
                    'status' => $order->status,
                    'status_label' => $this->getStatusLabel($order->status),
                    'status_color' => $this->getStatusColor($order->status),
                    'total' => $order->total,
                    'formatted_total' => 'Rp ' . number_format((float) $order->total, 0, ',', '.'),
                    'created_at' => $order->created_at->format('d M Y, H:i'),
                    'items_count' => $order->orderItems->count(),
                    'first_product' => $order->orderItems->first() ? [
                        'name' => $order->orderItems->first()->product->name,
                        'image' => $order->orderItems->first()->product->image_path,
                        'quantity' => $order->orderItems->first()->quantity
                    ] : null,
                    'payment_status' => $order->payment ? $order->payment->status : null,
                    'can_pay' => $order->status === 'pending' && (!$order->payment || $order->payment->status === 'pending')
                ];
            });

        return Inertia::render('Customer/MyOrders', [
            'orders' => $orders
        ]);
    }

    /**
     * Create order from buy now
     */
    public function buyNow(Request $request)
    {
        try {
            $request->validate([
                'product_id' => 'required|exists:products,id',
                'quantity' => 'required|integer|min:1|max:99'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);
        }

        $product = Product::findOrFail($request->product_id);

        // Check stock availability
        if ($product->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Stok tidak mencukupi'
            ], 400);
        }

        DB::beginTransaction();
        
        try {
            // Calculate total
            $total = $product->price * $request->quantity;

            // Create order
            $order = Order::create([
                'user_id' => Auth::id(),
                'status' => 'pending',
                'total' => $total,
                'note' => json_encode([
                    'source' => 'buy_now',
                    'created_via' => 'web'
                ])
            ]);

            // Create order item
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'subtotal' => $total
            ]);

            // Create pending payment
            Payment::create([
                'order_id' => $order->id,
                'user_id' => Auth::id(),
                'method' => null, // Will be set when user selects payment method
                'status' => 'pending',
                'meta' => json_encode([
                    'created_at' => now(),
                    'source' => 'buy_now'
                ])
            ]);

            // Reduce stock (reserve stock for pending payment)
            $product->decrement('stock', $request->quantity);

            DB::commit();

            // Return success response for Inertia
            return back()->with('success', 'Pesanan berhasil dibuat');

        } catch (\Exception $e) {
            DB::rollback();
            
            Log::error('Buy now error: ' . $e->getMessage(), [
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return error response for Inertia
            return back()->withErrors([
                'message' => 'Gagal membuat pesanan: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show order detail for payment
     */
    public function show($id)
    {
        $order = Order::with(['orderItems.product.category', 'payment'])
            ->where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        $orderData = [
            'id' => $order->id,
            'order_number' => 'ORD-' . strtoupper(substr($order->id, 0, 8)),
            'status' => $order->status,
            'status_label' => $this->getStatusLabel($order->status),
            'status_color' => $this->getStatusColor($order->status),
            'total' => $order->total,
            'formatted_total' => 'Rp ' . number_format((float) $order->total, 0, ',', '.'),
            'created_at' => $order->created_at->format('d M Y, H:i'),
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
            'can_pay' => $order->status === 'pending' && (!$order->payment || $order->payment->status === 'pending')
        ];

        // Available payment methods
        $paymentMethods = [
            [
                'code' => 'gopay',
                'name' => 'GoPay',
                'logo' => '/images/payments/gopay.png',
                'description' => 'Bayar dengan GoPay'
            ],
            [
                'code' => 'dana',
                'name' => 'DANA',
                'logo' => '/images/payments/dana.png',
                'description' => 'Bayar dengan DANA'
            ],
            [
                'code' => 'shopeepay',
                'name' => 'ShopeePay',
                'logo' => '/images/payments/shopeepay.png',
                'description' => 'Bayar dengan ShopeePay'
            ],
            [
                'code' => 'ovo',
                'name' => 'OVO',
                'logo' => '/images/payments/ovo.png',
                'description' => 'Bayar dengan OVO'
            ],
            [
                'code' => 'bca',
                'name' => 'BCA',
                'logo' => '/images/payments/bca.png',
                'description' => 'Transfer Bank BCA'
            ]
        ];

        return Inertia::render('Customer/PaymentDetail', [
            'order' => $orderData,
            'paymentMethods' => $paymentMethods
        ]);
    }

    /**
     * Process payment
     */
    public function processPayment(Request $request, $id)
    {
        $request->validate([
            'payment_method' => 'required|in:gopay,dana,shopeepay,ovo,bca'
        ]);

        $order = Order::with('payment')
            ->where('user_id', Auth::id())
            ->where('id', $id)
            ->where('status', 'pending')
            ->firstOrFail();

        if (!$order->payment || $order->payment->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak dapat diproses'
            ], 400);
        }

        DB::beginTransaction();
        
        try {
            // Update payment
            $order->payment->update([
                'method' => $request->payment_method,
                'status' => 'success',
                'paid_at' => now(),
                'meta' => json_encode([
                    'method' => $request->payment_method,
                    'paid_at' => now(),
                    'processed_via' => 'web'
                ])
            ]);

            // Update order status to progress (diproses)
            $order->update(['status' => 'progress']);

            // Update product sold count
            foreach ($order->orderItems as $item) {
                $item->product->increment('sold', $item->quantity);
            }

            // Create wallet transaction and update balance
            $this->createWalletTransaction($order);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil diproses'
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses pembayaran'
            ], 500);
        }
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
     * Create wallet transaction and update balance after successful payment
     */
    private function createWalletTransaction($order)
    {
        // Get or create main wallet (single wallet system)
        $wallet = Wallet::firstOrCreate(
            [], // No specific condition, get the first wallet or create one
            ['balance' => 0]
        );

        // Create wallet transaction record
        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'order_id' => $order->id,
            'amount' => $order->total,
            'description' => "Payment received for order #{$order->id}",
        ]);

        // Update wallet balance
        $wallet->increment('balance', $order->total);

        Log::info('Wallet transaction created', [
            'order_id' => $order->id,
            'amount' => $order->total,
            'new_balance' => $wallet->fresh()->balance
        ]);
    }

    /**
     * Download invoice PDF
     */
    public function downloadInvoice($id)
    {
        $order = Order::with(['orderItems.product.category', 'payment', 'user'])
            ->where('user_id', Auth::id())
            ->where('id', $id)
            ->firstOrFail();

        // Check if order is paid
        if (!$order->payment || $order->payment->status !== 'success') {
            return back()->withErrors(['message' => 'Invoice hanya tersedia untuk pesanan yang sudah dibayar']);
        }

        $data = [
            'order' => $order,
            'order_number' => 'ORD-' . strtoupper(substr($order->id, 0, 8)),
            'company_name' => 'Furnisia',
            'company_address' => 'Jl. Furniture No. 123, Jakarta Selatan',
            'company_phone' => '+62 21 1234-5678',
            'company_email' => 'contact@furnisia.com',
            'invoice_date' => now()->format('d M Y'),
            'due_date' => $order->payment->paid_at->format('d M Y'),
        ];

        $pdf = PDF::loadView('invoice.template', $data);
        $pdf->setPaper('A4', 'portrait');
        
        $filename = 'Invoice-' . $data['order_number'] . '.pdf';
        
        return $pdf->download($filename);
    }
}