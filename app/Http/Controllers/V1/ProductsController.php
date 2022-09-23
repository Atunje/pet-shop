<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\V1\FilterRequest;
use App\Http\Requests\V1\ProductRequest;
use App\Http\Resources\V1\ProductResource;
use App\Models\Product;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ProductsController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/products",
     *      operationId="ListProducts",
     *      tags={"Products"},
     *      summary="Fetch products",
     *      @OA\Parameter(
     *          name="page",
     *          in="query",
     *          @OA\Schema(
     *             type="integer",
     *         ),
     *      ),
     *      @OA\Parameter(
     *          name="limit",
     *          in="query",
     *          @OA\Schema(
     *             type="integer",
     *         ),
     *      ),
     *      @OA\Parameter(
     *          name="sort_by",
     *          in="query",
     *          @OA\Schema(
     *             type="integer",
     *         ),
     *      ),
     *      @OA\Parameter(
     *          name="desc",
     *          in="query",
     *          @OA\Schema(
     *             type="boolean",
     *          ),
     *      ),
     *      @OA\Response(response=200, description="OK"),
     *      @OA\Response(response=401, description="Unauthorized"),
     *      @OA\Response(response=404, description="Page Not Found"),
     *      @OA\Response(response=422, description="Unprocessable Entity"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     * Display a listing of the resource.
     *
     * @return JsonResponse
     * @throws \Exception
     */
    public function index(FilterRequest $request)
    {
        $filter_params = $request->filterParams();
        $data = ProductResource::collection(Product::getAll($filter_params))->resource;

        return $this->jsonResponse(data:$data);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/product/create",
     *      operationId="CreateProduct",
     *      tags={"Products"},
     *      summary="Create a new product",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  required={
     *                      "category_uuid",
     *                      "price",
     *                      "title",
     *                      "description",
     *                      "metadata"
     *                  },
     *                  @OA\Property(property="category_uuid", type="string"),
     *                  @OA\Property(property="title", type="string"),
     *                  @OA\Property(property="description", type="string"),
     *                  @OA\Property(property="metadata", type="object"),
     *                  @OA\Property(property="price", type="string"),
     *              )
     *          )
     *      ),
     *      @OA\Response(response=200, description="OK"),
     *      @OA\Response(response=401, description="Unauthorized"),
     *      @OA\Response(response=404, description="Page Not Found"),
     *      @OA\Response(response=422, description="Unprocessable Entity"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     *
     * Store a newly created resource in storage.
     *
     * @param ProductRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProductRequest $request)
    {
        $product = Product::create($request->validFields());

        return $this->jsonResponse(data:new ProductResource($product));
    }

    /**
     * @OA\Get(
     *      path="/api/v1/product/{uuid}",
     *      operationId="FetchProduct",
     *      tags={"Products"},
     *      summary="Fetch a product",
     *      @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *      ),
     *      @OA\Response(response=200, description="OK"),
     *      @OA\Response(response=401, description="Unauthorized"),
     *      @OA\Response(response=404, description="Page Not Found"),
     *      @OA\Response(response=422, description="Unprocessable Entity"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     *
     * Display the specified product.
     *
     * @param Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Product $product)
    {
        return $this->jsonResponse(data: new ProductResource($product));
    }

    /**
     * @OA\Put(
     *      path="/api/v1/product/{uuid}",
     *      operationId="UpdateProduct",
     *      tags={"Products"},
     *      summary="Update an existing product",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *      ),
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  required={
     *                      "category_uuid",
     *                      "price",
     *                      "title",
     *                      "description",
     *                      "metadata",
     *                  },
     *                  @OA\Property(property="category_uuid", type="string"),
     *                  @OA\Property(property="title", type="string"),
     *                  @OA\Property(property="description", type="string"),
     *                  @OA\Property(property="metadata", type="object"),
     *                  @OA\Property(property="price", type="string"),
     *              )
     *          )
     *      ),
     *      @OA\Response(response=200, description="OK"),
     *      @OA\Response(response=401, description="Unauthorized"),
     *      @OA\Response(response=404, description="Page Not Found"),
     *      @OA\Response(response=422, description="Unprocessable Entity"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     *
     * Update the specified resource in storage.
     *
     * @param ProductRequest $request
     * @param Product $product
     * @return JsonResponse
     */
    public function update(ProductRequest $request, Product $product)
    {
        if ($product->update($request->validFields())) {
            return $this->jsonResponse(data: $product);
        }

        return $this->jsonResponse(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     *@OA\Delete(
     *      path="/api/v1/product/{uuid}",
     *      operationId="DeleteProduct",
     *      tags={"Products"},
     *      summary="Delete a product",
     *      security={{"bearerAuth":{}}},
     *      @OA\Parameter(
     *         name="uuid",
     *         in="path",
     *         required=true,
     *      ),
     *      @OA\Response(response=200, description="OK"),
     *      @OA\Response(response=401, description="Unauthorized"),
     *      @OA\Response(response=404, description="Page Not Found"),
     *      @OA\Response(response=422, description="Unprocessable Entity"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     *
     * Remove the specified product from storage.
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
