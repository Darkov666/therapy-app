<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Package>
 */
class PackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $sessionCount = $this->faker->numberBetween(3, 10);
        $pricePerSession = $this->faker->randomFloat(2, 40, 150);

        return [
            'title' => $this->faker->words(3, true),
            'description' => $this->faker->sentence(),
            'price' => $pricePerSession * $sessionCount * 0.9, // 10% discount for packages
            'session_count' => $sessionCount,
            'is_active' => true,
            'image' => 'https://placehold.co/600x400/F3E5F5/4A148C?text=Package',
            'video_url' => null,
        ];
    }
}
