<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    /**
     * The application's global HTTP middleware stack.
     *
     * These middleware are run during every request to your application irrespective if it is web or api
     *there cannot be named middlewares
     * @var array
     */
    protected $middleware = [
        \Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class,
        \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
        \App\Http\Middleware\TrimStrings::class,
        \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        \App\Http\Middleware\TrustProxies::class,
    ];

    /**
     * The application's route middleware groups.
     * executed depending which group
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            'signature:X-Application-Name',
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        //named middlewares being called
        //you only can send parameters for the name middleware
        //using a colon allows you to set the parameter
        //order matters
        'api' => [
            'cors',//added
            'signature:X-Application-Name',//receives a single parameter, an after middleware, acts on the response
            'throttle:90,1',//receives two parameters,max attempts, decay minutes
            'bindings',
        ],
    ];

    /**
     * The application's route middleware.
     * these middlewares can be named
     * you register middlewares here
     * These middleware may be assigned to groups or used individually.
     *
     * @var array
     */
    protected $routeMiddleware = [
        'auth' => \App\Http\Middleware\Authenticate::class,
        'auth.basic' => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'bindings' => \Illuminate\Routing\Middleware\SubstituteBindings::class,
        'can' => \Illuminate\Auth\Middleware\Authorize::class,
        'cors' => \Barryvdh\Cors\HandleCors::class,//added
        'client.credentials' => \Laravel\Passport\Http\Middleware\CheckClientCredentials::class,//added
        'guest' => \App\Http\Middleware\RedirectIfAuthenticated::class,
        //'throttle' => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'throttle' => \App\Http\Middleware\CustomThrottleRequests::class,//replaced the original
        'scope' => \Laravel\Passport\Http\Middleware\CheckForAnyScope::class,//registering checkForAnyScope middleware (Scope is going to verify that a specific access token from their request has at least one of the scope)
        'scopes' => \Laravel\Passport\Http\Middleware\CheckScopes::class,//registering (it is going to verify if that single access token has all the scoops that we need)
        'signature' => \App\Http\Middleware\SignatureMiddleware::class,//added
        'transform.input' => \App\Http\Middleware\TransformInput::class,//registering a middleware
    ];

    /**
     * The priority-sorted list of middleware.
     *
     * This forces the listed middleware to always be in the given order.
     *
     * @var array
     */
    protected $middlewarePriority = [
        \Illuminate\Session\Middleware\StartSession::class,
        \Illuminate\View\Middleware\ShareErrorsFromSession::class,
        \App\Http\Middleware\Authenticate::class,
        \Illuminate\Session\Middleware\AuthenticateSession::class,
        \Illuminate\Routing\Middleware\SubstituteBindings::class,
        \Illuminate\Auth\Middleware\Authorize::class,
    ];
}
