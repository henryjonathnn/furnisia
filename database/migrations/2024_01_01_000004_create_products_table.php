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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('restrict');
            $table->string('name', 191); // Limit for better indexing
            $table->text('description');
            $table->decimal('price', 15, 2)->unsigned(); // No negative prices
            $table->integer('stock')->unsigned()->default(0); // No negative stock
            $table->string('image_path', 500)->nullable();
            $table->boolean('is_active')->default(true);
            $table->json('specs')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
            
            // Indexes for fast search, filter, sort
            $table->index(['is_active', 'deleted_at']); // Active products filter
            $table->index(['category_id', 'is_active']); // Category filter
            $table->index(['price', 'is_active']); // Price sorting
            $table->index('name'); // Name search
            $table->index('stock'); // Stock management
            $table->index('created_at'); // Latest products
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
