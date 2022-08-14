<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  Request  $request
     * @param Throwable $e
     * @return  SymfonyResponse
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e): SymfonyResponse
    {
        if ($e instanceof ModelNotFoundException) {
            $model = $this->getModelName($e->getModel());
            return $this->createResponse(
                __('http.model_not_found', ["model" => $model]),
                SymfonyResponse::HTTP_NOT_FOUND
            );
        }

        return match (true) {
            $e instanceof NotFoundHttpException, $e instanceof MethodNotAllowedHttpException => $this->createResponse(
                __('http.not_found'),
                SymfonyResponse::HTTP_NOT_FOUND
            ),
            $e instanceof UnauthorizedException,
            $e instanceof AuthorizationException,
            $e instanceof AuthenticationException => $this->createResponse(
                __('http.unauthorized'),
                SymfonyResponse::HTTP_UNAUTHORIZED
            ),
            default => parent::render($request, $e)
        };
    }

    /**
     * @param class-string $model_path
     * @return string
     */
    private function getModelName(string $model_path)
    {
        $path = explode("\\", $model_path);
        $index = count($path) - 1;
        return $path[$index];
    }

    private function createResponse(mixed $error, int $status_code): JsonResponse
    {
        return response()->json(['success' => 0, 'error' => $error], $status_code);
    }
}
