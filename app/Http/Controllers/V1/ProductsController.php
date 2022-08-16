<?php

namespace App\Http\Controllers\V1;

use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\V1\ProductRequest;
use App\Http\Resources\V1\ProductResource;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $per_pg = $request->has('limit') ? intval($request->limit) : 10;
        $data = ProductResource::collection(Product::getAll($request->all(), $per_pg))->resource;

        return $this->jsonResponse(data:$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param ProductRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProductRequest $request)
    {
        $inputs = $request->all();
        $inputs['metadata'] = json_decode(strval($request->metadata), true);

        $product = Product::create($inputs);
        return $this->jsonResponse(data:new ProductResource($product));
    }

    /**
     * Display the specified resource.
     *
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Product $product)
    {
        return $this->jsonResponse(data: new ProductResource($product));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ProductRequest $request
     * @param Product $product
     * @return JsonResponse
     */
    public function update(ProductRequest $request, Product $product)
    {
        if ($product->update($request->all())) {
            return $this->jsonResponse(data: $product);
        }

        return $this->jsonResponse(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Product $product
     * @return JsonResponse
     */
    public function destroy(Product $product)
    {
        if ($product->delete()) {
            return $this->jsonResponse();
        }

        return $this->jsonResponse(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
