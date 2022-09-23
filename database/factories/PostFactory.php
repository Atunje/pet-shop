<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $title = fake()->sentence(rand(1, 20));

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'content' => fake()->text(),
            'metadata' => [
                'author' => fake()->name(),
            ],
        ];
    }
}
