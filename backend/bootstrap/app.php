<?php

use App\Http\Middleware\ForceJsonResponseMiddleware;
use App\Http\Middleware\JwtMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        channels: __DIR__ . '/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            "token" => JwtMiddleware::class,
        ]);
        $middleware->api(append: [
            "json" => ForceJsonResponseMiddleware::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (ValidationException $throwable) {
            return jsonResponse(
                status: 422,
                message: $throwable->getMessage(),
                errors: $throwable->errors()
            );
        });

        $exceptions->render(function (AccessDeniedHttpException $throwable) {
            return jsonResponse(
                status: 403,
                message: $throwable->getMessage(),
            );
        });
    })->create();
