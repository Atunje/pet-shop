<?php

namespace App\Http\Resources\V1;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Product
 */
class ProductResource extends JsonResource
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
            'title' => $this->title,
            'price' => $this->price,
            'description' => $this->description,
            'image' => new FileResource($this->getImageFile()),
            'category' => new CategoryResource($this->category),
            'brand' => new BrandResource($this->getBrand()),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
