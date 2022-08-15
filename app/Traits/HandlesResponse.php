<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Resources\Json\JsonResource;

trait HandlesResponse
{
    /**
     * Prepare json response
     *
     * @param int $status_code
     * @param mixed|array|JsonResource $data
     * @param string|mixed|null $error
     * @param array $errors
     * @param array $trace
     * @return JsonResponse
     */
    protected function jsonResponse(
        $status_code = Response::HTTP_OK,
        $data = [],
        $error = null,
        $errors = [],
        $trace = []
    ) {
        return response()->json([
            'success' => $status_code >= 200 && $status_code <= 299 ? 1 : 0,
            'data' => $data,
            'error' => $error,
            'errors' => $errors,
            'trace' => $trace,
        ], $status_code);
    }
}
