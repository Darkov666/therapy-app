<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Service;
use App\Models\Product;
use App\Models\Category;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $digitalTypes = ['ebook', 'video', 'manual', 'audio', 'product'];

        $services = Service::whereIn('type', $digitalTypes)->get();

        // Cache product categories
        $categories = Category::where('type', 'product')->get()->keyBy(function ($item) {
            return strtolower($item->name); // e.g. 'ebooks', 'videos'
        });

        foreach ($services as $service) {
            // Determine category
            $targetCategory = null;
            $serviceType = strtolower($service->type);

            // Map service type to category name logic
            // Assuming seeded categories: Ebooks, Videos, Manuales, Audios
            $catName = match ($serviceType) {
                'ebook' => 'ebooks',
                'video' => 'videos',
                'manual' => 'manuales',
                'audio' => 'audios',
                default => null
            };

            if ($catName && isset($categories[$catName])) {
                $targetCategory = $categories[$catName];
            } else {
                // Fallback: Try to find a category with same name as service type or just assign first available
                $targetCategory = $categories->first();
            }

            // Create Product
            Product::create([
                'user_id' => $service->user_id,
                'category_id' => $targetCategory ? $targetCategory->id : null,
                'title' => $service->title,
                'slug' => $service->id . '-' . \Illuminate\Support\Str::slug($service->title), // Ensure unique slug
                'description' => $service->description ?? '',
                'price_mxn' => $service->price_mxn ?? 0,
                'cover_path' => $service->image, // Service uses 'image' column
                'file_path' => null, // No file path in Service
                'type' => $service->type, // Preserve type string
                'is_active' => $service->is_active,
            ]);

            // Delete from services
            $service->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // One-way migration, no generic down as logic is destructive (deleted services)
        // Could technically recreate them but ID's would change.
    }
};
