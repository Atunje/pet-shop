<?php

namespace App\Http\Controllers\V1;

use App\Models\OrderStatus;
use Illuminate\Http\Request;
use App\Http\Requests\V1\OrderStatusRequest;
use App\Http\Resources\V1\OrderStatusResource;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class OrderStatusesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $per_pg = $request->has('limit') ? intval($request->limit) : 10;
        $data = OrderStatusResource::collection(OrderStatus::getAll($request->all(), $per_pg))->resource;

        return $this->jsonResponse(data:$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param OrderStatusRequest $request
     * @return JsonResponse
     */
    public function store(OrderStatusRequest $request)
    {
        $order_status = OrderStatus::create($request->all());
        return $this->jsonResponse(data:new OrderStatusResource($order_status));
    }

    /**
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
     * Update the specified resource in storage.
     *
     * @param OrderStatusRequest $request
     * @param OrderStatus $order_status
     * @return JsonResponse
     */
    public function update(OrderStatusRequest $request, OrderStatus $order_status)
    {
        if ($order_status->update($request->all())) {
            return $this->jsonResponse(data: $order_status);
        }

        return $this->jsonResponse(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
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
