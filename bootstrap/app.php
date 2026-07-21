<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withProviders([
        \Illuminate\View\ViewServiceProvider::class,
    ])
    ->booted(function (Application $app) {
        if (! $app->bound('view')) {
            $app->register(\Illuminate\View\ViewServiceProvider::class);
        }
    })
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(fn () => true);

        $exceptions->render(function (MongoDB\Driver\Exception\ConnectionTimeoutException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Database connection failed. Please check server configuration.',
            ], 500);
        });
    })->create();
