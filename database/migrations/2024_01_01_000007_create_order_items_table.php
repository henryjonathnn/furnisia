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
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('restrict');
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
