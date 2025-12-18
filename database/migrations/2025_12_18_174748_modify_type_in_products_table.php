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
        Schema::table('products', function (Blueprint $table) {
            // Change enum to string to allow custom types
            $table->string('type')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Revert to enum (might fail if custom values exist, but standard practice for down)
            // We'll leave it as string or try to convert back if needed, but for now strict revert:
            //$table->enum('type', ['ebook', 'video', 'manual', 'audio', 'other'])->change();
            // Safer to just leave it as string in down or simply drop the change logic if not critical. 
            $table->string('type')->change();
        });
    }
};
