<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        [$items, $amount] = $this->generateProductItems();
        $order_status = OrderStatus::all()->random();

        $payment_uuid = null;
        if ($order_status->title === 'shipped' || $order_status->title === 'paid') {
            $payment_uuid = Payment::factory()->create()->uuid;
        }

        return [
            'order_status_uuid' => $order_status->uuid,
            'payment_uuid' => $payment_uuid,
            'products' => $items,
            'amount' => $amount,
            'address' => fake()->address(),
            'delivery_fee' => $amount > 500 ? 0 : 15,
            'shipped_at' => $order_status->title == 'shipped' ? now() : null,
        ];
    }

    private function generateProductItems(): array
    {
        $products = $this->getProducts(rand(1, 5));

        $items = [];
        $total_amount = 0;

        foreach ($products as $product) {
            $item = [
                'uuid' => $product->uuid,
                'product' => $product->title,
                'price' => $product->price,
                'quantity' => rand(1, 40),
            ];

            $total_amount += $item['price'] * $item['quantity'];

            $items[] = $item;
        }

        return [$items, $total_amount];
    }

    private function getProducts($count)
    {
        $products = Product::all()->random($count);

        if ($products->count() < $count) {
            //create new ones
            $n = $count - $products->count();
            $new = Product::factory($n)->create();

            $products->merge($new);
        }

        return $products;
    }
}
