<?php

namespace App\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'products' => $this->products,
            'address' => $this->address,
            'delivery_fee' => $this->delivery_fee,
            'amount' => $this->amount,
            'user' => new UserResource($this->user),
            'order_status' => new OrderStatusResource($this->orderStatus),
            'payment' => new PaymentResource($this->payment),
            'shipped_at' => $this->shipped_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
