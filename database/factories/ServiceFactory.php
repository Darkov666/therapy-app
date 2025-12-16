<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Service>
 */
class ServiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = $this->faker->randomFloat(2, 50, 200);
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'price' => $price,
            'price_mxn' => $price * 20, // Approx conversion
            'price_usd' => $price,
            'duration_minutes' => $this->faker->randomElement([30, 50, 60, 90]),
            'type' => $this->faker->randomElement(['individual', 'couple', 'family', 'special']),
            'is_active' => true,
            'image' => 'https://placehold.co/600x400/E3F2FD/1565C0?text=Service',
        ];
    }
}
