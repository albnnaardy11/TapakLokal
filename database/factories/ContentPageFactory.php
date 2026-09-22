<?php
namespace Database\Factories;

use App\Models\ContentPage;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ContentPage> */
class ContentPageFactory extends Factory
{
    public function definition(): array
    {
        return ['type' => 'blog', 'title' => fake()->sentence(3), 'slug' => fake()->unique()->slug(), 'body' => fake()->paragraphs(3, true), 'status' => 'draft', 'position' => 0];
    }
}

