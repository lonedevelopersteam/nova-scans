<?php

namespace App\Http\Middleware;

use Exception;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TackleAdminExists
{
    public function handle(Request $request, Closure $next)
    {
        try {
            // Check if user is already logged in
            if ($request->cookie('users_login')) {
                Log::info('User already logged in via cookie');
                return $next($request);
            }

            // Make internal API call to check admin existence
            $apiResponse = $this->checkAdminExists();

            $message = $apiResponse['message'] ?? '';

            // Handle different response scenarios
            if ($message == "Admin users data exists.") {
                Log::warning('Unexpected API response: ' . $message);
                return to_route('login');
            }

        } catch (Exception $e) {
            Log::error('Error in TackleAdminExists middleware: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return to_route('login');
        }

        return $next($request);
    }

    private function checkAdminExists(): ?array
    {
        try {
            $internalRequest = Request::create('/api/v1/users/admin-exists');
            $internalRequest->headers->set('Accept', 'application/json');

            // Get API key from config instead of env() for better performance
            $apiKey = config('app.api_key') ?? env('API_KEY');
            if ($apiKey) {
                $internalRequest->headers->set('Authorization', $apiKey);
            }

            $apiResponse = app()->handle($internalRequest);

            // Check if response status is successful
            if ($apiResponse->getStatusCode() !== 200) {
                Log::error('Admin exists API returned non-200 status: ' . $apiResponse->getStatusCode());
                return null;
            }

            $responseContent = $apiResponse->getContent();
            $responseData = json_decode($responseContent, true);

            // Check if response is valid JSON
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Invalid JSON response from admin-exists API', [
                    'content' => $responseContent,
                    'json_error' => json_last_error_msg()
                ]);
                return null;
            }

            return $responseData;

        } catch (Exception $e) {
            Log::error('Error checking admin existence via API: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }
}
