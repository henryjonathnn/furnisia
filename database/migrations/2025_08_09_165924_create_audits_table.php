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
        Schema::create('audits', function (Blueprint $table) {
            $table->id();
            $table->uuid('user_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('restrict'); // Who made the change
            $table->string('auditable_type'); // Model class name (e.g., App\Models\Product)
            $table->string('auditable_id'); // ID of the model being audited (can be UUID or integer)
            $table->string('event'); // create, update, delete
            $table->json('old_values')->nullable(); // Previous values
            $table->json('new_values')->nullable(); // New values
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['auditable_type', 'auditable_id']); // Find audits for specific model
            $table->index('user_id'); // Find audits by user
            $table->index('event'); // Find audits by action type
            $table->index('created_at'); // Timeline sorting
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audits');
    }
};
