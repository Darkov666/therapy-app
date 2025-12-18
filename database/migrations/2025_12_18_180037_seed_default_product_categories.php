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
        $categories = [
            ['name' => 'Ebooks', 'slug' => 'ebooks', 'type' => 'product', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Videos', 'slug' => 'videos', 'type' => 'product', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Manuales', 'slug' => 'manuales', 'type' => 'product', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Audios', 'slug' => 'audios', 'type' => 'product', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ];

        \Illuminate\Support\Facades\DB::table('categories')->insert($categories);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::table('categories')
            ->whereIn('slug', ['ebooks', 'videos', 'manuales', 'audios'])
            ->delete();
    }
};
