<?php

namespace App\Exceptions;

use App\Traits\HandlesResponse;
use ErrorException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    use HandlesResponse;

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
     * @return  Response
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e): Response
    {
        return match (true) {
            $e instanceof ModelNotFoundException => $this->jsonResponse(
                status_code: Response::HTTP_NOT_FOUND,
                error: __('http.model_not_found', ['model' => $this->getModelName($e->getModel())])
            ),
            $e instanceof NotFoundHttpException, $e instanceof MethodNotAllowedHttpException => $this->jsonResponse(
                status_code: Response::HTTP_NOT_FOUND,
                error: __('http.not_found')
            ),
            $e instanceof ErrorException => $this->jsonResponse(status_code: Response::HTTP_INTERNAL_SERVER_ERROR),
            $e instanceof UnauthorizedException,
            $e instanceof AuthorizationException,
            $e instanceof AuthenticationException => $this->jsonResponse(
                status_code: Response::HTTP_UNAUTHORIZED,
                error: __('http.unauthorized')
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
        $path = explode('\\', $model_path);
        $index = count($path) - 1;

        return $path[$index];
    }
}
