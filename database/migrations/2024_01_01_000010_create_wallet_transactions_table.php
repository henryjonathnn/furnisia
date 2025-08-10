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
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('wallet_id')->constrained('wallets')->onDelete('cascade');
            $table->uuid('order_id');
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade'); // Always related to sales
            $table->decimal('amount', 15, 2)->unsigned(); // Always positive (sales revenue)
            $table->string('description', 255)->default('Sales Revenue'); // Simple description
            $table->timestamps();
            
            // Simple indexes for sales tracking
            $table->index(['wallet_id', 'created_at']); // Transaction history
            $table->index('order_id'); // Order-related revenue
            $table->index('created_at'); // Sales timeline
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_transactions');
    }
};
