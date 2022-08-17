<?php

namespace App\Http\Controllers\V1;

use App\Http\Requests\V1\FilterRequest;
use App\Http\Requests\V1\OrderRequest;
use App\Http\Services\V1\OrderService;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\V1\OrderResource;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class OrdersController extends Controller
{
    public function __construct(private readonly OrderService $orderService)
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     *
     */
    public function index(FilterRequest $request)
    {
        $filter_params = $request->validated();
        return $this->jsonResponse(data:$this->orderService->getAll($filter_params));
    }


    /**
     * Display a listing of the resource.
     *
     *
     */
    public function dashboad(FilterRequest $request)
    {
        $filter_params = $request->validated();
        return $this->jsonResponse(data:$this->orderService->getAll($filter_params));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param OrderRequest $request
     * @return JsonResponse
     */
    public function store(OrderRequest $request)
    {
        $order = $this->orderService->create($request->all());
        if ($order !== null) {
            return $this->jsonResponse(data: new OrderResource($order));
        }

        return $this->jsonResponse(Response::HTTP_UNPROCESSABLE_ENTITY, error: __('orders.creation_failed'));
    }


    /**
     * Display the specified resource.
     *
     * @param Order $order
     * @return JsonResponse
     */
    public function show(Order $order)
    {
        return $this->jsonResponse(data: new OrderResource($order));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param OrderRequest $request
     * @param Order $order
     * @return JsonResponse
     */
    public function update(OrderRequest $request, Order $order)
    {
        if ($this->orderService->update($order, $request->all())) {
            return $this->jsonResponse(data: new OrderResource($order));
        }

        return $this->jsonResponse(Response::HTTP_UNPROCESSABLE_ENTITY, error: __('orders.update_failed'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Order $order
     * @return JsonResponse
     * @throws Throwable
     */
    public function destroy(Order $order)
    {
        if ($this->orderService->delete($order)) {
            return $this->jsonResponse();
        }

        return $this->jsonResponse(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
