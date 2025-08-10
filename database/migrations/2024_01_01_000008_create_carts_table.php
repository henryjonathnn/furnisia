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
        Schema::create('carts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('product_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->integer('quantity')->unsigned()->default(1); // No negative quantity
            $table->boolean('is_selected')->default(false);
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
            
            // Unique constraint to prevent duplicate cart items
            $table->unique(['user_id', 'product_id', 'deleted_at'], 'unique_user_product_cart');
            
            // Indexes for cart operations
            $table->index(['user_id', 'is_selected']); // Selected items
            $table->index(['user_id', 'deleted_at']); // User cart items
            $table->index('updated_at'); // Recently updated items
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};
