<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\JsonResponse;

class ManageUserController extends Controller
{
    public function showManageAdmin(): View
    {
        return view('users.admin');
    }
    public function showManageEditor(): View
    {
        return view('users.editor');
    }
    public function showManageReader(): View
    {
        return view('users.reader');
    }
    public function getUsers(Request $request): JsonResponse
    {
        $usersLoginCookie = $request->cookie('users_login');

        if (!$usersLoginCookie) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated. Cookie not found.'
            ], 401);
        }

        $userData = json_decode($usersLoginCookie, true);
        $accessToken = $userData['access_token'] ?? null;

        if (!$accessToken) {
            return response()->json([
                'success' => false,
                'message' => 'Access token not found in cookie.'
            ], 401);
        }

        try {
            $internalRequest = Request::create(
                '/api/v1/users',
                'GET',
                [
                    'role' => $request->input('role', 'Admin'),
                    'page' => $request->input('page', 1),
                    'per_page' => $request->input('per_page', 20)
                ]
            );

            // Set authorization header
            $internalRequest->headers->set('Authorization', "$accessToken");
            $internalRequest->headers->set('Accept', 'application/json');

            // Dispatch request
            $response = app()->handle($internalRequest);

            return response()->json(
                json_decode($response->getContent(), true),
                $response->getStatusCode()
            );

        } catch (\Exception $e) {
            Log::error('Internal API Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve admin data',
                'error_details' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }
    public function searchUser(Request $request): JsonResponse
    {
        $usersLoginCookie = $request->cookie('users_login');

        if (!$usersLoginCookie) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated. Cookie not found.'
            ], 401);
        }

        $userData = json_decode($usersLoginCookie, true);
        $accessToken = $userData['access_token'] ?? null;

        if (!$accessToken) {
            return response()->json([
                'success' => false,
                'message' => 'Access token not found in cookie.'
            ], 401);
        }

        try {
            $internalRequest = Request::create(
                '/api/v1/users/search',
                'GET',
                [
                    'q' => $request->input('q'),
                    'role' => $request->input('role', 'admin'),
                ]
            );

            // Set authorization header
            $internalRequest->headers->set('Authorization', "$accessToken");
            $internalRequest->headers->set('Accept', 'application/json');

            // Dispatch request
            $response = app()->handle($internalRequest);

            return response()->json(
                json_decode($response->getContent(), true),
                $response->getStatusCode()
            );

        } catch (\Exception $e) {
            Log::error('Internal API Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve admin data',
                'error_details' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }
    public function createUser(Request $request): JsonResponse
    {
        $accessToken = $request->input('api_key');
        $role = $request->input('role');
        $apiUrl = "/api/v1/users/register-{$role}";

        // Validate the request input
        $validatedData = $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        if (!$accessToken) {
            return response()->json([
                'success' => false,
                'message' => 'Access token not found.'
            ], 401);
        }

        try {
            // Data to be sent as JSON body
            $jsonPayload = json_encode([
                'email' => $validatedData['email'],
                'username' => $validatedData['username'],
                'password' => $validatedData['password'],
            ]);

            // Create a new Request instance with a JSON body
            $internalRequest = Request::create(
                $apiUrl,
                'POST',
                [], // Empty parameters array
                [], // Empty cookies array
                [], // Empty files array
                [
                    'HTTP_Authorization' => $accessToken,
                    'HTTP_Accept' => 'application/json',
                    'CONTENT_TYPE' => 'application/json',
                ],
                $jsonPayload
            );

            // Dispatch the request
            $response = app()->handle($internalRequest);

            $this->clearCache($accessToken);

            return response()->json(
                json_decode($response->getContent(), true),
                $response->getStatusCode()
            );

        } catch (\Exception $e) {
            Log::error('Internal API Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to create admin user. ' . $e->getMessage(),
                'error_details' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }
    public function deleteUser(Request $request, string $userId): JsonResponse
    {
        $usersLoginCookie = $request->cookie('users_login');
        $apiKey = $request->input('api_key');

        if (!$usersLoginCookie) {
            return response()->json([
                'success' => false,
                'message' => 'User not authenticated. Cookie not found.'
            ], 401);
        }

        $userData = json_decode($usersLoginCookie, true);
        $accessToken = $userData['access_token'] ?? null;

        if (!$accessToken) {
            return response()->json([
                'success' => false,
                'message' => 'Access token not found in cookie.'
            ], 401);
        }

        try {
            $internalRequest = Request::create(
                '/api/v1/users/'.$userId,
                'DELETE',
            );

            // Set authorization header
            $internalRequest->headers->set('Authorization', "$accessToken");
            $internalRequest->headers->set('Accept', 'application/json');

            // Dispatch request
            $response = app()->handle($internalRequest);
            $this->clearCache(env('API_KEY'));

            return response()->json(
                json_decode($response->getContent(), true),
                $response->getStatusCode()
            );

        } catch (\Exception $e) {
            Log::error('Internal API Error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user data',
                'error_details' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }
    private function clearCache(string $apiKey): void {
        $internalRequest = Request::create(
            '/api/v1/manga/clearCache',
            'DELETE',
        );

        // Set authorization header
        $internalRequest->headers->set('Authorization', "$apiKey");
        $internalRequest->headers->set('Accept', 'application/json');

        app()->handle($internalRequest);
    }
}
