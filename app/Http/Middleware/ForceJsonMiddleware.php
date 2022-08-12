<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class ForceJsonMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure(Request): (Response|RedirectResponse) $next
     * @return Response|RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $header_accept = strval($request->header('accept'));
        if(! Str::contains($header_accept, ['/json', '+json'])) {
            $request->headers->set('accept', 'application/json');
        }
        return $next($request);
    }
}
