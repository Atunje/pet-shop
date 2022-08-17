<?php

namespace App\Http\Controllers\V1;

use App\Exceptions\InvalidOrderProductException;
use App\Http\Requests\V1\OrderRequest;
use App\Http\Services\V1\OrderService;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\V1\OrderResource;
use Symfony\Component\HttpFoundation\Response;

class OrdersController extends Controller
{
    public function __construct(private readonly OrderService $orderService)
    {
        //
    }

    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        return $this->jsonResponse(data:$this->orderService->getAll($request->all()));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param OrderRequest $request
     * @return JsonResponse
     */
    public function store(OrderRequest $request)
    {
        try {
            $order = $this->orderService->create($request->all());
            if ($order !== null) {
                return $this->jsonResponse(data: new OrderResource($order));
            }

            return $this->jsonResponse(Response::HTTP_UNPROCESSABLE_ENTITY, error: __('orders.creation_failed'));
        } catch (InvalidOrderProductException $e) {
            return $this->jsonResponse(Response::HTTP_UNPROCESSABLE_ENTITY, $e->getMessage());
        }
    }
}
