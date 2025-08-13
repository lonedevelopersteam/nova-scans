<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->hasCookie('users_login')) {
            $cookieValue = $request->cookie('users_login');

            Log::info('Cookie "nama_cookie_anda" ditemukan dengan nilai: ' . $cookieValue);

            return $next($request);
        }

        return redirect('/login');
    }
}
