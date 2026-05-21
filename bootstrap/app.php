<?php

declare(strict_types=1);

use App\Exceptions\Handler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web:      __DIR__ . '/../routes/web.php',
        api:      __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health:   '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Stateless API: disable CSRF verification for the api guard
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // All exceptions routed through our single, uniform JSON handler
        $exceptions->render(
            fn (\Throwable $e, \Illuminate\Http\Request $request): ?\Illuminate\Http\JsonResponse
                => $request->expectsJson() ? Handler::render($e, $request) : null
        );
    })
    ->create();
