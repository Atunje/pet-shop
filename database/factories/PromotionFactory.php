<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Promotion>
 */
class PromotionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'title' => fake()->sentence(rand(1, 20)),
            'content' => fake()->text(500),
            'metadata' => [
                'valid_to' => fake()->date(),
                'valid_fom' => fake()->date(),
            ],
        ];
    }
}
