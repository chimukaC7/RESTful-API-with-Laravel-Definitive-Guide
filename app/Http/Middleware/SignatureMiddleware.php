<?php

namespace App\Http\Middleware;

use Closure;

class SignatureMiddleware
{
    //creating a Custom Middleware
    /**
     * Handle an incoming request.
     * this is an after middleware,it is acting after the response
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string $headerName
     * @return mixed
     */
    public function handle($request, Closure $next, $headerName = 'X-Name')
    {
        $response = $next($request);

        //adding a custom header
        $response->headers->set($headerName, config('app.name'));

        return $response;
    }
}
