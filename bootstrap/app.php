<?php

use App\Http\Middleware\CheckAdminExists;
use App\Http\Middleware\CheckApiKey;
use App\Http\Middleware\CheckLogin;
use App\Http\Middleware\TackleAdminExists;
use App\Http\Middleware\TokenMiddleware;
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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'api_key' => CheckApiKey::class,
            'token' => TokenMiddleware::class,
            'admin_exists' => CheckAdminExists::class,
            'tackle_admin_exists' => TackleAdminExists::class,
            'check_login' => CheckLogin::class,
        ]);
        $middleware->encryptCookies(except: [
            'users_login',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
