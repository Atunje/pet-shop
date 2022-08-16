<?php

namespace App\Http\Controllers\V1;

use App\Models\Payment;
use Illuminate\Http\Request;
use App\Http\Requests\V1\PaymentRequest;
use App\Http\Resources\V1\PaymentResource;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class PaymentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $per_pg = $request->has('limit') ? intval($request->limit) : 10;
        $data = PaymentResource::collection(Payment::getAll($request->all(), $per_pg))->resource;

        return $this->jsonResponse(data:$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param PaymentRequest $request
     * @return JsonResponse
     */
    public function store(PaymentRequest $request)
    {
        $inputs = $request->all();
        $inputs['details'] = json_decode(strval($request->details));

        $payment = Payment::create($inputs);
        return $this->jsonResponse(data:new PaymentResource($payment));
    }

    /**
     * Display the specified resource.
     *
     * @param Payment $payment
     * @return JsonResponse
     */
    public function show(Payment $payment)
    {
        return $this->jsonResponse(data: new PaymentResource($payment));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param PaymentRequest $request
     * @param Payment $payment
     * @return JsonResponse
     */
    public function update(PaymentRequest $request, Payment $payment)
    {
        $inputs = $request->all();
        $inputs['details'] = json_decode(strval($request->details));

        if ($payment->update($inputs)) {
            return $this->jsonResponse(data: $payment);
        }

        return $this->jsonResponse(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Payment $payment
     * @return JsonResponse
     */
    public function destroy(Payment $payment)
    {
        if ($payment->delete()) {
            return $this->jsonResponse();
        }

        return $this->jsonResponse(Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}
