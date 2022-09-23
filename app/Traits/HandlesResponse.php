<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Symfony\Component\HttpFoundation\Response;

trait HandlesResponse
{
    /**
     * Prepare json response.
     *
     * @param int $status_code
     * @param mixed $data
     * @param mixed|null $error
     * @param array $errors
     * @param array $trace
     * @return JsonResponse
     */
    protected function jsonResponse(
        int $status_code = Response::HTTP_OK,
        mixed $data = [],
        mixed $error = null,
        array $errors = [],
        array $trace = []
    ): JsonResponse {
        return response()->json([
            'success' => $status_code >= 200 && $status_code <= 299 ? 1 : 0,
            'data' => $data,
            'error' => $error,
            'errors' => $errors,
            'trace' => $trace,
        ], $status_code);
    }
}
