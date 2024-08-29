<?php

use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
            \Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets::class,
        ]);

        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->respond(function (Response $response, Throwable $exception, Request $request) {
            // -> Para entornos de PRODUCTION...
            // if (! app()->environment(['local', 'testing']) && in_array($response->getStatusCode(), [500, 503, 404, 403])) {
            // -> Para cualquier entorno...
            // if (in_array($response->getStatusCode(), [500, 503, 404, 403])) {
            //     return Inertia::render('Error', ['status' => $response->getStatusCode()])
            //         ->toResponse($request)
            //         ->setStatusCode($response->getStatusCode());
            // } elseif ($response->getStatusCode() === 419) {
            //     return back()->with([
            //         'message' => 'The page expired, please try again.',
            //     ]);
            // }

            // return $response;

            // ============================================================================================

            $status = $response->getStatusCode();

            return match ($status) {
                401 => Inertia::render('Errors/401')->toResponse($request)->setStatusCode($status),
                403 => Inertia::render('Errors/403')->toResponse($request)->setStatusCode($status),
                404 => Inertia::render('Errors/404')->toResponse($request)->setStatusCode($status),
                500, 503 => Inertia::render('Errors/500')->toResponse($request)->setStatusCode($status),
                419 => redirect()->back()->withErrors(['status' => __('The page expired, please try again.')]),

                default => $response,
            };
        });
    })->create();
