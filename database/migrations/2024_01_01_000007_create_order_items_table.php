<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id');
            $table->uuid('product_id');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('restrict');
            $table->integer('quantity')->unsigned(); // No negative quantity
            $table->decimal('subtotal', 15, 2)->unsigned(); // No negative subtotal
            $table->timestamps();
            
            // Indexes for order analysis
            $table->index('order_id'); // Order details lookup
            $table->index('product_id'); // Product sales analysis
            $table->index(['product_id', 'created_at']); // Product sales timeline
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
