<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->hasHeader('Authorization')) {
            Log::warning('Authorization header missing.');
            return new Response('Unauthorized: Authorization header missing.', 401);
        }

        $authorizationHeader = $request->header('Authorization');

        $expectedApiKey = env('API_KEY');

        if (!$expectedApiKey) {
            Log::error('API_KEY not set in .env file.');
            return new Response('Server Error: API Key not configured.', 500);
        }

        if ($authorizationHeader !== $expectedApiKey) {
            Log::warning('Invalid API KEY provided.', ['provided_api' => $authorizationHeader]);
            return new Response('Unauthorized: Invalid API Key.', 401);
        }

        return $next($request);
    }
}
