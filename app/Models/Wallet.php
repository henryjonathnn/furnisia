<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = [
        'balance',
    ];

    protected $casts = [
        'balance' => 'decimal:2',
    ];

    /**
     * Get all wallet transactions
     */
    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }

    /**
     * Get formatted balance
     */
    public function getFormattedBalanceAttribute()
    {
        return 'Rp ' . number_format($this->balance, 0, ',', '.');
    }

    /**
     * Add revenue from completed order
     */
    public function addRevenue($amount, $orderId)
    {
        $this->balance += $amount;
        $this->save();

        // Create transaction record
        $this->transactions()->create([
            'order_id' => $orderId,
            'amount' => $amount,
            'description' => 'Sales Revenue',
        ]);
    }
}
