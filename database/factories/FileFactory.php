<?php

namespace Database\Factories;

use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<File>
 */
class FileFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $name = Str::random(40);
        $ext = 'png'; //fake()->randomElement(['png', 'pdf', 'jpg']);
        $path = 'public/pet-shop/'.$name.'.'.$ext;

        return [
            'name' => $name,
            'path' => $path,
            'size' => rand(15, 50),
            'type' => 'image/png',
        ];
    }
}
