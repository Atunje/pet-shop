<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\V1\FilterRequest;
use App\Http\Requests\V1\OrderStatusRequest;
use App\Http\Resources\V1\OrderStatusResource;
use App\Models\OrderStatus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class OrderStatusesController extends Controller
{
    /**
     * @OA\Get(
     *      path="/api/v1/order-statuses",
     *      operationId="ListOrderStatuses",
     *      tags={"Order Statuses"},
     *      summary="Fetch order statuses",
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
     */
    public function index(FilterRequest $request)
    {
        $filter_params = $request->filterParams();
        $data = OrderStatusResource::collection(OrderStatus::getAll($filter_params))->resource;

        return $this->jsonResponse(data:$data);
    }

    /**
     * @OA\Post(
     *      path="/api/v1/order-status/create",
     *      operationId="CreateOrderStatus",
     *      tags={"Order Statuses"},
     *      summary="Create a new order status",
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  required={
     *                      "title",
     *                  },
     *                  @OA\Property(property="title", type="string"),
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
     * @param OrderStatusRequest $request
     * @return JsonResponse
     */
    public function store(OrderStatusRequest $request)
    {
        $order_status = OrderStatus::create($request->validFields());

        return $this->jsonResponse(data:new OrderStatusResource($order_status));
    }

    /**
     * @OA\Get(
     *      path="/api/v1/order-status/{uuid}",
     *      operationId="FetchOrder",
     *      tags={"Order Statuses"},
     *      summary="Fetch an order status",
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
     * Display the specified resource.
     *
     * @param OrderStatus $order_status
     * @return JsonResponse
     */
    public function show(OrderStatus $order_status)
    {
        return $this->jsonResponse(data: new OrderStatusResource($order_status));
    }

    /**
     * @OA\Put(
     *      path="/api/v1/order-status/{uuid}",
     *      operationId="UpdateOrderStatus",
     *      tags={"Order Statuses"},
     *      summary="Update an existing order status",
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
     *                      "title",
     *                  },
     *                  @OA\Property(property="title", type="string"),
     *              )
     *          )
     *      ),
     *      @OA\Response(response=200, description="OK"),
     *      @OA\Response(response=401, description="Unauthorized"),
     *      @OA\Response(response=404, description="Page Not Found"),
     *      @OA\Response(response=422, description="Unprocessable Entity"),
     *      @OA\Response(response=500, description="Internal Server Error")
     * )
     * Update the specified resource in storage.
     *
     * @param OrderStatusRequest $request
     * @param OrderStatus $order_status
     * @return JsonResponse
     */
    public function update(OrderStatusRequest $request, OrderStatus $order_status)
    {
        if ($order_status->update($request->validFields())) {
            return $this->jsonResponse(data: $order_status);
        }

        return $this->jsonResponse(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * @OA\Delete(
     *      path="/api/v1/order-status/{uuid}",
     *      operationId="DeleteOrder",
     *      tags={"Order Statuses"},
     *      summary="Delete an order status",
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
     * Remove the specified resource from storage.
     *
     * @param OrderStatus $order_status
     * @return JsonResponse
     */
    public function destroy(OrderStatus $order_status)
    {
        if ($order_status->delete()) {
            return $this->jsonResponse();
        }

        return $this->jsonResponse(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
