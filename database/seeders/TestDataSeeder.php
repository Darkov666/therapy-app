<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Package;
use App\Models\BlogPost;
use App\Models\BlogTopic;
use App\Models\User;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Generate Services
        Service::factory()->count(10)->create();

        // Generate Packages (Shop)
        Package::factory()->count(10)->create();

        // Ensure we have some topics
        $topics = BlogTopic::factory()->count(5)->create();

        // Ensure we have a user for posts
        $user = User::first() ?? User::factory()->create();

        // Generate Blog Posts
        BlogPost::factory()->count(20)->state(function (array $attributes) use ($topics, $user) {
            return [
                'topic_id' => $topics->random()->id,
                'user_id' => $user->id,
            ];
        })->create();
    }
}
