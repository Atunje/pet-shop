<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class APIVersion
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string $guard
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next, string $guard)
    {
        config(['app.api.version' => $guard]);

        return $next($request);
    }
}
