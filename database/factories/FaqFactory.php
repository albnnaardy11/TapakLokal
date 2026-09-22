<?php
namespace Database\Factories;

use App\Models\Faq;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Faq> */
class FaqFactory extends Factory
{
    public function definition(): array
    {
        return ['category' => 'Umum', 'question' => fake()->sentence().'?', 'answer' => fake()->paragraph(), 'status' => 'draft', 'position' => 0];
    }
}

