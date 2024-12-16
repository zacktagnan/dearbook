<?php

use App\Exceptions\ForbiddenAreaException;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
// use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

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
        // No vale :: ini
        // $exceptions->render(function (BadRequestHttpException $e, Request $request) {
        //     return 'BadRequestHttpException';
        //     // return Inertia::render('Errors/other')->toResponse($request)->setStatusCode($response->getStatusCode());
        // });
        // No vale :: fin
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

            // dump('getStatusCode', $response->getStatusCode());
            // dump('exception', $exception);
            // // dump('exception->message', 'Excepción: ' .  $exception->message);
            // // dump('exception->previous', $exception->previous->message);
            // dd();

            // Vale pero no del todo :: ini
            // if ($exception instanceof BadRequestException || $exception instanceof BadRequestHttpException) {
            //     return Inertia::render('Errors/other', [
            //         // Así, se imprime el mensaje predeterminado de la excepción capturada
            //         // pero no el mensaje personalizado pasado por el constructor
            //         'status' => $response->getStatusCode() . $exception->getMessage(),
            //     ])->toResponse($request)->setStatusCode($response->getStatusCode());
            // }
            // Vale pero no del todo :: fin

            $statusCode = $response->getStatusCode();
            // $message = null;
            // $message = $exception->getMessage() ? $exception->getMessage() : null;
            // o más simple (con el operador de coalescencia nula, devolviendo el valor de la izquierda del operador si no es NULL, sino, se devolverá el de la derecha)
            // $message = $exception->getMessage() ?? null;
            // o más simple aún porque getMessage, automáticamente, devuelve NULL si no se envía un mensaje desde la excepción lanzada
            $message = $exception->getMessage();

            if ($exception instanceof ForbiddenAreaException) {
                $statusCode = $exception->getStatusCode();
                $message = $exception->getMessage();
            }

            return match ($statusCode) {
                // 401 => Inertia::render('Errors/401')->toResponse($request)->setStatusCode($statusCode),
                // 403 => Inertia::render('Errors/403')->toResponse($request)->setStatusCode($statusCode),
                // 404 => Inertia::render('Errors/404')->toResponse($request)->setStatusCode($statusCode),
                // 500, 503 => Inertia::render('Errors/500')->toResponse($request)->setStatusCode($statusCode),
                // ----------------------------------------------------------------------------------------------------
                // 401, 403, 404, 500, 503 => Inertia::render('Errors/' . $statusCode, [
                //     'statusCode' => $statusCode,
                // ])->toResponse($request)->setStatusCode($statusCode),
                // ----------------------------------------------------------------------------------------------------
                401, 403, 404, 503 => Inertia::render('Errors/ErrorCode', [
                    'statusCode' => $statusCode,
                    'message' => $message,
                ])->toResponse($request)->setStatusCode($statusCode),
                500 => Inertia::render('Errors/ErrorCode', [
                    'statusCode' => $statusCode,
                    'message' => $message,
                ])->toResponse($request)->setStatusCode($statusCode),
                419 => redirect()->back()->withErrors(['status' => __('The page expired, please try again.')]),

                default => $response,
            };
        });
    })->create();
