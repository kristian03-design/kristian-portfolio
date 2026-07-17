<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureAdminUser;
use App\Http\Middleware\SecurityHeaders;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(SecurityHeaders::class);
        $middleware->alias([
            'admin' => EnsureAdminUser::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(function ($response, \Throwable $e, \Illuminate\Http\Request $request) {
            if (!config('app.debug') && ($request->expectsJson() || $request->is('api/*') || $request->ajax())) {
                $status = method_exists($e, 'getStatusCode') ? $e->getStatusCode() : 500;

                // Let Laravel's ValidationException flow through untouched
                if ($e instanceof \Illuminate\Validation\ValidationException) {
                    return $response;
                }

                // Clean response for HTTP exceptions
                if ($e instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface) {
                    return response()->json([
                        'message' => $e->getMessage() ?: \Symfony\Component\HttpFoundation\Response::$statusTexts[$status],
                    ], $status);
                }

                // Mask database, file, and code exceptions in production
                return response()->json([
                    'message' => 'An internal server error occurred. Please try again later.',
                ], 500);
            }
            return $response;
        });
    })->create();

if ($storagePath = env('LARAVEL_STORAGE_PATH')) {
    $app->useStoragePath($storagePath);
}

return $app;
