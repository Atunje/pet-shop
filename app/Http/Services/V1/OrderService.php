<?php

namespace App\Http\Services\V1;

use App\DTOs\FilterParams;
use App\Http\Resources\V1\OrderResource;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\User;
use DB;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Throwable;

class OrderService
{
    /**
     * @var int
     */
    private $free_delivery_threshold = 500;

    /**
     * @var int
     */
    private $delivery_fee = 15;

    /**
     * Currently logged in user.
     *
     * @var User
     */
    private $user;

    /**
     * Constructor.
     */
    public function __construct(Request $request)
    {
        if ($request->user() !== null) {
            $this->user = $request->user();
        }
    }

    /**
     * @param FilterParams $filter_params
     * @return mixed
     * @throws Exception
     */
    public function getAll(FilterParams $filter_params): mixed
    {
        if (! $this->user->isAdmin()) {
            //always filter orders by the logged in user
            $filter_params->__set('user_uuid', $this->user->uuid);
        }

        return OrderResource::collection(Order::getAll($filter_params))->resource;
    }

    /**
     * Creates a new order record.
     *
     * @param array $data
     * @return Order|null
     */
    public function create(array $data): ?Order
    {
        $order = new Order($data);
        $order->amount = $this->calculateAmount($data['products']);
        $order->products = $data['products'];
        $order->delivery_fee = $this->getDeliveryFee($order);
        $order->user_uuid = $this->user->uuid;
        $order->shipped_at = $this->getShippingTime($data['order_status_uuid']);

        if ($order->save()) {
            return $order;
        }

        return null;
    }

    private function getDeliveryFee(Order $order): int
    {
        return $order->amount > $this->free_delivery_threshold ? 0 : $this->delivery_fee;
    }

    private function getShippingTime(string $status_uuid): ?Carbon
    {
        return OrderStatus::isShippedStatus($status_uuid) ? now() : null;
    }

    /**
     * Calculate the total amount of the products and populate the products with product name and price.
     *
     * @param array $products
     * @return float
     */
    private function calculateAmount(array &$products): float
    {
        $amount = 0;

        foreach ($products as $i => $item) {
            /** @var Product $product */
            $product = Product::where('uuid', $item['uuid'])->first();

            //set product and price on the item
            $item['product'] = $product->title;
            $item['price'] = $product->price;
            $products[$i] = $item;

            //update the total amount
            $amount += $item['price'] * $item['quantity'];
        }

        return round($amount, 2);
    }

    /**
     * Update order record.
     *
     * @param Order $order
     * @param array $data
     * @return bool
     */
    public function update(Order $order, array $data): bool
    {
        $order->amount = $this->calculateAmount($data['products']);
        $order->delivery_fee = $this->getDeliveryFee($order);
        $order->shipped_at = $this->getShippingTime($data['order_status_uuid']);

        return $order->update($data);
    }

    /**
     * Delete order record and the payment attached to it.
     *
     * @param Order $order
     * @throws Throwable
     * @return bool
     */
    public function delete($order): bool
    {
        return (bool) DB::transaction(function () use ($order) {
            //delete the payment
            $order->payment()->delete();
            //delete the order
            return $order->delete();
        });
    }
}
