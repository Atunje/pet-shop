<?php

namespace Database\Factories;

use App\Models\File;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->LastName(),
            'email' => fake()->safeEmail(),
            'email_verified_at' => now(),
            'avatar' => File::factory()->create()->uuid,
            'phone_number' => fake()->phoneNumber(),
            'address' => fake()->address(),
            'is_marketing' => rand(0, 1),
            'is_admin' => false,
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            //'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the user's is_marketing field is true.
     *
     * @return static
     */
    public function marketing()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_marketing' => true,
            ];
        });
    }

    /**
     * Indicate that the user is admin.
     *
     * @return static
     */
    public function admin()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_admin' => true,
            ];
        });
    }

    /**
     * Indicate that the user's is_marketing field is true and is admin.
     *
     * @return static
     */
    public function admin_marketing()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_marketing' => true,
                'is_admin' => true,
            ];
        });
    }
}
