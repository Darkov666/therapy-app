<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique(); // For URLs
            $table->text('description')->nullable();

            // Pricing
            $table->decimal('price_mxn', 10, 2);

            // Files
            $table->string('cover_path')->nullable(); // Public image
            $table->string('file_path')->nullable();  // Private file (the product itself)

            // Metadata
            $table->enum('type', ['ebook', 'video', 'manual', 'audio', 'other']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
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
