<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'type' => 'credit_card',
            'details' => [
                'ccv' => rand(3,3),
                'number' => fake()->creditCardNumber(),
                'expire_date' => fake()->creditCardExpirationDate,
                'holder_name' => fake()->name()
            ]
        ];
    }

    public function cash_on_delivery()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'cash_on_delivery',
                'details' => [
                    'address' => fake()->address(),
                    'first_name' => fake()->firstName(),
                    'last_name' => fake()->lastName()
                ]
            ];
        });
    }

    public function credit_card()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'credit_card',
                'details' => [
                    'ccv' => rand(3,3),
                    'number' => fake()->creditCardNumber(),
                    'expire_date' => fake()->creditCardExpirationDate,
                    'holder_name' => fake()->name()
                ]
            ];
        });
    }

    public function bank_transfer()
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'bank_transfer',
                'details' => [
                    'iban' => strtoupper(Str::random(19)),
                    'name' => fake()->name(),
                    'swift' => strtoupper(Str::random(11)),
                ]
            ];
        });
    }
}
