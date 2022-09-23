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
        $type = fake()->randomElement(['credit_card', 'cash_on_delivery', 'bank_transfer']);

        return [
            'type' => $type,
            'details' => $this->getDetails($type),
        ];
    }

    private function getDetails($type)
    {
        if ($type == 'credit_card') {
            return $this->credit_card_details();
        }

        if ($type == 'cash_on_delivery') {
            return $this->cash_on_delivery_details();
        }

        if ($type == 'bank_transfer') {
            return $this->bank_transfer_details();
        }

        return null;
    }

    private function bank_transfer_details(): array
    {
        return [
            'iban' => strtoupper(Str::random(19)),
            'name' => fake()->name(),
            'swift' => strtoupper(Str::random(11)),
        ];
    }

    private function cash_on_delivery_details(): array
    {
        return [
            'address' => fake()->address(),
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
        ];
    }

    private function credit_card_details(): array
    {
        return [
            'ccv' => rand(3, 3),
            'number' => fake()->creditCardNumber(),
            'expire_date' => fake()->creditCardExpirationDate,
            'holder_name' => fake()->name(),
        ];
    }

    public function cash_on_delivery(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'cash_on_delivery',
                'details' => $this->cash_on_delivery_details(),
            ];
        });
    }

    public function credit_card(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'credit_card',
                'details' => $this->credit_card_details(),
            ];
        });
    }

    public function bank_transfer(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'type' => 'bank_transfer',
                'details' => $this->bank_transfer_details(),
            ];
        });
    }
}
