<?php

use App\Http\Middleware\SetLocale;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\AdminMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'admin'     => AdminMiddleware::class,
            'setLocale' => SetLocale::class,
        ]);

        EncryptCookies::except('favorieten');

        $middleware->prependToGroup(
            'web',
            \Illuminate\Session\Middleware\StartSession::class
        );

        $middleware->appendToGroup(
            'web',
            SetLocale::class
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
