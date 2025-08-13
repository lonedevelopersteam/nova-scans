<?php

namespace App\Http\Middleware;

use App\Models\Users;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class TokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->hasHeader('Authorization')) {
            Log::warning('Authorization header missing.');
            return new Response('Unauthorized: Authorization header missing.', 401);
        }

        $authorizationHeader = $request->header('Authorization');

        $user = Users::where('access_token', $authorizationHeader)->first();
        if (!$user) {
            Log::warning('Invalid API KEY provided.', ['provided_api' => $authorizationHeader]);
            return new Response('Unauthorized: You need to be login', 401);
        }

        if ($user->access_token_expire <= now()) {
            Log::warning('Expired API KEY provided.', ['provided_api' => $authorizationHeader]);
            return new Response('Token expired', 403);
        }

        return $next($request);
    }
}
