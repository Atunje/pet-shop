<?php

namespace App\Http\Services\V1;

use App\DTOs\FilterParams;
use DB;
use Throwable;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Resources\V1\OrderResource;
use Illuminate\Validation\UnauthorizedException;

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
     * Currently logged in user
     *
     * @var User
     */
    private $user;

    /**
     * Constructor
     */
    public function __construct(Request $request)
    {
        if($request->user() === null) {
            throw new UnauthorizedException();
        }

        $this->user = $request->user();
    }

    /**
     * @param FilterParams $filter_params
     * @return mixed
     */
    public function getAll($filter_params)
    {
        if (! $this->user->isAdmin()) {
            //always filter orders by the logged in user
            $filter_params->__set('user_uuid', $this->user->uuid);
        }

        return OrderResource::collection(Order::getAll($filter_params))->resource;
    }

    /**
     * Creates a new order record
     *
     * @param array $data
     * @return Order|null
     */
    public function create($data): ?Order
    {
        $order = new Order($data);
        $order->amount = $this->calculateAmount($data['products']);
        $order->products = $data['products'];
        $order->delivery_fee = $order->amount > $this->free_delivery_threshold ? 0 : $this->delivery_fee;
        $order->user_uuid = $this->user->uuid;
        $order->shipped_at = " ";

        return $order->save() ? $order : null;
    }

    /**
     * Calculate the total amount of the products and populate the products with product name and price
     *
     * @param array $products
     * @return float
     */
    private function calculateAmount(array &$products): float
    {
        $amount = 0;

        foreach ($products as $i => $item) {

            $product = Product::where('uuid', $item['uuid'])->first();

            if ($product !== null) {
                //set product and price on the item
                $item['product'] = $product->title;
                $item['price'] = $product->price;
                $products[$i] = $item;

                //update the total amount
                $amount += $item['price'] * $item['quantity'];
            }
        }

        return round($amount, 2);
    }

    /**
     * Update order record
     *
     * @param Order $order
     * @param array $data
     * @return bool
     */
    public function update(Order $order, array $data)
    {
        $order->amount = $this->calculateAmount($data['products']);
        $order->delivery_fee = $order->amount > $this->free_delivery_threshold ? 0 : $this->delivery_fee;

        return $order->update($data);
    }

    /**
     * Delete order record and the payment attached to it
     *
     * @param Order $order
     * @throws Throwable
     * @return bool
     */
    public function delete($order)
    {
        return (bool) DB::transaction(function () use ($order) {
            //delete the payment
            $order->payment()->delete();
            //delete the order
            return $order->delete() ?? false;
        });
    }
}