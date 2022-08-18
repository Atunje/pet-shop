<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        //pick from existing or create a new one
        $category = Category::all()->random()->first();
        if($category == null) {
            $category = Category::factory()->create();
        }

        //pick from existing or create a new one
        $brand = Brand::all()->random()->first();
        if($brand == null) {
            $brand = Brand::factory()->create();
        }

        return [
            'title' => fake()->sentence(rand(1,6)),
            'price' => fake()->randomFloat(2,2, 3),
            'description' => fake()->text(),
            'category_uuid' => $category->uuid,
            'metadata' => [
                'brand' => $brand->uuid,
                'image' => File::factory()->create()->uuid
            ],
        ];
    }
}
