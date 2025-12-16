<?php

namespace Database\Factories;

use App\Models\BlogTopic;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BlogPost>
 */
class BlogPostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->sentence();
        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => $this->faker->paragraphs(3, true),
            'excerpt' => $this->faker->paragraph(),
            'image' => 'https://placehold.co/600x400/E0F2F1/00695C?text=Blog+Post',
            'read_time' => $this->faker->numberBetween(3, 10) . ' min',
            'is_published' => true,
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'user_id' => User::factory(),
            'topic_id' => BlogTopic::factory(), // Assuming we might want a factory for topics too, or we can pick random existing
        ];
    }
}
