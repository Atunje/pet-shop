<?php

namespace App\Http\Services\V1;

use _PHPStan_9a6ded56a\Nette\Neon\Exception;
use App\Exceptions\InvalidOrderProductException;
use App\Http\Resources\V1\OrderResource;
use App\Models\Order;
use App\Models\Product;
use Auth;

class OrderService
{
    /**
     * @param $filter_params
     * @return mixed
     */
    public function getAll($filter_params)
    {
        $per_pg = isset($filter_params['limit']) ? intval($filter_params['limit']) : 10;

        $user = Auth::user();
        if(! $user->isAdmin()) {
            $filter_params['user_uuid'] = $user->uuid;
        }

        return OrderResource::collection(Order::getAll($filter_params, $per_pg))->resource;
    }

    /**
     * Creates a new order record
     *
     * @param array $data
     * @return Order|null
     * @throws InvalidOrderProductException
     */
    public function create($data): ?Order
    {
        //get the total amount
        $amount = $this->calculateAmount($data['products']);

        $order = new Order($data);
        $order->amount = $amount;
        $order->delivery_fee = $amount > 500 ? 0 : 15;
        $order->user_uuid = Auth::user()->uuid;
        $order->shipped_at = " ";

        return $order->save() ? $order : null;
    }

    /**
     * @params array $products
     * @throws InvalidOrderProductException
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

            $amount += $item['price'] * $item['quantity'];
        }

        return round($amount, 2);
    }
}
