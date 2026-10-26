<?php

use App\Http\Middleware\AdminMiddleware;
use App\Http\Middleware\AuthorMiddleware;
use App\Http\Middleware\CheckSessionActivity;
use App\Http\Middleware\CheckSuspended;
use App\Http\Middleware\SecurityHeadersMiddleware;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(SecurityHeadersMiddleware::class);
        $middleware->web(append: [
            CheckSessionActivity::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'fedapay/webhook',
        ]);
        $middleware->alias([
            'admin' => AdminMiddleware::class,
            'author' => AuthorMiddleware::class,
            'not-suspended' => CheckSuspended::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
