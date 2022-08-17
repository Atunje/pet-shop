<?php

namespace App\Http\Services\V1;

use App\Http\Resources\V1\OrderResource;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Auth;
use DB;
use Illuminate\Contracts\Auth\Authenticatable;
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
     * Currently logged in user
     *
     * @var User|Authenticatable
     */
    private $user;

    public function __construct()
    {
        $this->user = Auth::user();
    }

    /**
     * @param $filter_params
     * @return mixed
     */
    public function getAll($filter_params)
    {
        if(! $this->user->isAdmin()) {
            //always filter orders by the logged in user
            $filter_params['user_uuid'] = $this->user->uuid;
        }

        $per_pg = isset($filter_params['limit']) ? intval($filter_params['limit']) : 10;
        return OrderResource::collection(Order::getAll($filter_params, $per_pg))->resource;
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
        $order->user_uuid = Auth::user()->uuid;
        $order->shipped_at = " ";

        return $order->save() ? $order : null;
    }

    /**
     * Calculate the total amount of the products and populate the products with product name and price
     *
     * @params array $products
     */
    private function calculateAmount(array &$products): float|int
    {
        $amount = 0;

        foreach($products as $i => $item) {
            //get product
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
        return DB::transaction(function() use ($order) {
            //delete the payment
            $order->payment()->delete();
            //delete the order
            return $order->delete() ?? false;
        });
    }

}
