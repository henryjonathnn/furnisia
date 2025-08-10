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
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('order_id')->unique();
            $table->uuid('user_id');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('restrict'); // One payment per order
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict');
            $table->enum('method', ['gopay', 'dana', 'shopeepay', 'ovo', 'bca'])->nullable();
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->timestamp('paid_at')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
            
            // Indexes for payment tracking
            $table->index(['status', 'method']); // Payment method analysis
            $table->index(['user_id', 'status']); // User payment history
            $table->index('paid_at'); // Payment timeline
            $table->index('created_at'); // Payment order
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
