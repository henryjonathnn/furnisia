<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class CartController extends Controller
{
    /**
     * Display the user's cart
     */
    public function index()
    {
        $cartItems = Cart::with(['product.category'])
            ->where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($cart) {
                $product = $cart->product;
                $subtotal = $product->price * $cart->quantity;
                
                return [
                    'id' => $cart->id,
                    'product_id' => $product->id,
                    'name' => $product->name,
                    'image' => $product->image_path,
                    'price' => $product->price,
                    'formatted_price' => 'Rp ' . number_format((float) $product->price, 0, ',', '.'),
                    'quantity' => $cart->quantity,
                    'stock' => $product->stock,
                    'subtotal' => $subtotal,
                    'formatted_subtotal' => 'Rp ' . number_format($subtotal, 0, ',', '.'),
                    'category' => $product->category?->name,
                    'stock_status' => $this->getStockStatus($product->stock),
                    'is_selected' => $cart->is_selected,
                    'slug' => \Str::slug($product->name),
                    'max_quantity' => min($product->stock, 99) // Max 99 or stock available
                ];
            });

        // Calculate totals
        $selectedItems = $cartItems->where('is_selected', true);
        $totalItems = $selectedItems->sum('quantity');
        $totalPrice = $selectedItems->sum('subtotal');
        $totalSavings = 0; // Could implement discount logic here

        return Inertia::render('Customer/Cart', [
            'cartItems' => $cartItems->values(),
            'summary' => [
                'total_items' => $totalItems,
                'total_price' => $totalPrice,
                'formatted_total_price' => 'Rp ' . number_format($totalPrice, 0, ',', '.'),
                'total_savings' => $totalSavings,
                'formatted_total_savings' => 'Rp ' . number_format($totalSavings, 0, ',', '.'),
                'items_count' => $cartItems->count(),
                'selected_items_count' => $selectedItems->count()
            ]
        ]);
    }

    /**
     * Add product to cart
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:99'
        ]);

        $product = Product::findOrFail($request->product_id);

        // Check stock availability
        if ($product->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Stok tidak mencukupi'
            ], 400);
        }

        // Check if item already exists in cart
        $existingCart = Cart::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->whereNull('deleted_at')
            ->first();

        if ($existingCart) {
            // Update quantity
            $newQuantity = $existingCart->quantity + $request->quantity;
            
            if ($newQuantity > $product->stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Jumlah melebihi stok yang tersedia'
                ], 400);
            }

            $existingCart->update([
                'quantity' => min($newQuantity, 99), // Max 99
                'is_selected' => true
            ]);
        } else {
            // Create new cart item
            Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'is_selected' => true
            ]);
        }

        // Get updated cart count
        $cartCount = Cart::where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->sum('quantity');

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil ditambahkan ke keranjang',
            'cart_count' => $cartCount
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:99'
        ]);

        $cart = Cart::where('id', $id)
            ->where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->firstOrFail();

        $product = $cart->product;

        if ($request->quantity > $product->stock) {
            return response()->json([
                'success' => false,
                'message' => 'Jumlah melebihi stok yang tersedia'
            ], 400);
        }

        $cart->update(['quantity' => $request->quantity]);

        return response()->json([
            'success' => true,
            'message' => 'Keranjang berhasil diperbarui'
        ]);
    }

    /**
     * Toggle cart item selection
     */
    public function toggleSelection(Request $request, $id)
    {
        $cart = Cart::where('id', $id)
            ->where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->firstOrFail();

        $cart->update(['is_selected' => !$cart->is_selected]);

        return response()->json([
            'success' => true,
            'is_selected' => $cart->is_selected
        ]);
    }

    /**
     * Select/Deselect all cart items
     */
    public function toggleSelectAll(Request $request)
    {
        $selectAll = $request->boolean('select_all');

        Cart::where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->update(['is_selected' => $selectAll]);

        return response()->json([
            'success' => true,
            'select_all' => $selectAll
        ]);
    }

    /**
     * Remove item from cart
     */
    public function destroy($id)
    {
        $cart = Cart::where('id', $id)
            ->where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->firstOrFail();

        $cart->update(['deleted_at' => now()]);

        // Get updated cart count
        $cartCount = Cart::where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->sum('quantity');

        return response()->json([
            'success' => true,
            'message' => 'Produk berhasil dihapus dari keranjang',
            'cart_count' => $cartCount
        ]);
    }

    /**
     * Remove selected items from cart
     */
    public function destroySelected()
    {
        Cart::where('user_id', Auth::id())
            ->where('is_selected', true)
            ->whereNull('deleted_at')
            ->update(['deleted_at' => now()]);

        // Get updated cart count
        $cartCount = Cart::where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->sum('quantity');

        return response()->json([
            'success' => true,
            'message' => 'Produk terpilih berhasil dihapus dari keranjang',
            'cart_count' => $cartCount
        ]);
    }

    /**
     * Get cart count for navbar
     */
    public function getCartCount()
    {
        if (!Auth::check()) {
            return response()->json(['count' => 0]);
        }

        $count = Cart::where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->sum('quantity');

        return response()->json(['count' => $count]);
    }

    /**
     * Checkout selected items from cart
     */
    public function checkout(Request $request)
    {
        try {
            // Get selected cart items
            $selectedCartItems = Cart::with('product')
                ->where('user_id', Auth::id())
                ->where('is_selected', true)
                ->whereNull('deleted_at')
                ->get();

            if ($selectedCartItems->isEmpty()) {
                return back()->withErrors([
                    'message' => 'Tidak ada produk yang dipilih untuk checkout'
                ]);
            }

            // Check stock availability for all selected items
            foreach ($selectedCartItems as $cartItem) {
                if ($cartItem->product->stock < $cartItem->quantity) {
                    return back()->withErrors([
                        'message' => "Stok {$cartItem->product->name} tidak mencukupi. Tersedia: {$cartItem->product->stock}, diminta: {$cartItem->quantity}"
                    ]);
                }
            }

            DB::beginTransaction();

            try {
                // Calculate total
                $total = $selectedCartItems->sum(function ($cartItem) {
                    return $cartItem->product->price * $cartItem->quantity;
                });

                // Create order
                $order = Order::create([
                    'user_id' => Auth::id(),
                    'status' => 'pending',
                    'total' => $total,
                    'note' => json_encode([
                        'source' => 'cart_checkout',
                        'created_via' => 'web',
                        'cart_items_count' => $selectedCartItems->count()
                    ])
                ]);

                // Create order items and reduce stock
                foreach ($selectedCartItems as $cartItem) {
                    // Create order item
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $cartItem->product->id,
                        'quantity' => $cartItem->quantity,
                        'subtotal' => $cartItem->product->price * $cartItem->quantity
                    ]);

                    // Reduce stock (reserve stock for pending payment)
                    $cartItem->product->decrement('stock', $cartItem->quantity);
                }

                // Create pending payment
                Payment::create([
                    'order_id' => $order->id,
                    'user_id' => Auth::id(),
                    'method' => null, // Will be set when user selects payment method
                    'status' => 'pending',
                    'meta' => json_encode([
                        'created_at' => now(),
                        'source' => 'cart_checkout',
                        'items_count' => $selectedCartItems->count()
                    ])
                ]);

                // Remove selected items from cart (soft delete)
                Cart::where('user_id', Auth::id())
                    ->where('is_selected', true)
                    ->whereNull('deleted_at')
                    ->update(['deleted_at' => now()]);

                DB::commit();

                // Return success response for Inertia
                return back()->with('success', 'Pesanan berhasil dibuat dari keranjang');

            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }

        } catch (\Exception $e) {
            \Log::error('Cart checkout error: ' . $e->getMessage(), [
                'user_id' => Auth::id(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors([
                'message' => 'Gagal membuat pesanan: ' . $e->getMessage()
            ]);
        }
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