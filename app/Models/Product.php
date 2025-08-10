<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Product extends Model
{
    use SoftDeletes, Auditable, HasUuids;

    protected $fillable = [
        'category_id',
        'name',
        'description',
        'price',
        'stock',
        'sold',
        'image_path',
        'is_active',
        'specs',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'sold' => 'integer',
        'is_active' => 'boolean',
        'specs' => 'array',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the category that the product belongs to
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all cart items for this product
     */
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Get all order items for this product
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Check if product is in stock
     */
    public function inStock()
    {
        return $this->stock > 0;
    }

    /**
     * Check if product is available (active and in stock)
     */
    public function isAvailable()
    {
        return $this->is_active && $this->inStock();
    }

    /**
     * Reduce stock when product is sold
     */
    public function reduceStock($quantity)
    {
        if ($this->stock >= $quantity) {
            $this->stock -= $quantity;
            $this->save();
            return true;
        }
        return false;
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }
}
