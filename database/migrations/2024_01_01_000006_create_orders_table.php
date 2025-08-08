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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->enum('status', ['pending', 'progress', 'completed', 'cancelled'])->default('pending');
            $table->decimal('total', 15, 2)->unsigned(); // No negative totals
            $table->string('nota_path', 500)->nullable();
            $table->json('note')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
            
            // Indexes for order management
            $table->index(['user_id', 'status']); // User orders by status
            $table->index(['status', 'created_at']); // Orders by status and date
            $table->index(['total', 'status']); // Revenue analysis
            $table->index('created_at'); // Order timeline
            $table->index('deleted_at'); // Soft delete queries
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
