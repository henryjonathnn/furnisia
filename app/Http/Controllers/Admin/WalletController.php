<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    /**
     * Get wallet balance for admin navbar
     */
    public function getBalance()
    {
        $wallet = Wallet::first();
        
        if (!$wallet) {
            return response()->json([
                'balance' => 0,
                'formatted_balance' => 'Rp 0'
            ]);
        }

        return response()->json([
            'balance' => $wallet->balance,
            'formatted_balance' => 'Rp ' . number_format((float) $wallet->balance, 0, ',', '.')
        ]);
    }

    /**
     * Get wallet transaction history
     */
    public function transactions(Request $request)
    {
        $wallet = Wallet::first();
        
        if (!$wallet) {
            return response()->json([
                'transactions' => [],
                'total' => 0
            ]);
        }

        $query = WalletTransaction::with(['order.user'])
            ->where('wallet_id', $wallet->id)
            ->orderBy('created_at', 'desc');

        // Add filters if needed
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->paginate(20);

        $transactions->getCollection()->transform(function ($transaction) {
            return [
                'id' => $transaction->id,
                'amount' => $transaction->amount,
                'formatted_amount' => 'Rp ' . number_format($transaction->amount, 0, ',', '.'),
                'description' => $transaction->description,
                'order_id' => $transaction->order_id,
                'order_number' => 'ORD-' . strtoupper(substr($transaction->order_id, 0, 8)),
                'customer_name' => $transaction->order->user->name ?? 'Unknown',
                'created_at' => $transaction->created_at->format('d M Y, H:i'),
                'date' => $transaction->created_at->format('Y-m-d')
            ];
        });

        return response()->json($transactions);
    }
}