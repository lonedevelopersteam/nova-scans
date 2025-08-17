<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminEditorLoginRequest;
use App\Http\Requests\AdminRegisterRequest;
use App\Http\Requests\CheckLoginRequest;
use App\Http\Requests\EditorRegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UpdateDeviceRequest;
use App\Http\Requests\LogoutRequest;
use App\Http\Requests\OtpValidationRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\SendOtpRequest;
use App\Http\Requests\UpdatePasswordRequest;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;
use App\Http\Resources\UsersCollectionWithNoPagination;
use App\Mail\OtpMail;
use App\Models\Otp;
use App\Models\Users;
use Carbon\Carbon;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class UserController extends Controller
{
    private $otpExpiryMinutes = 5;

    public function checkAdminUsersExist(): void
    {
        $adminUsersExist = Users::where('role', 'Admin')->exists();

        throw new HttpResponseException(response([
            "success" => true,
            "message" => $adminUsersExist ? "Admin users data exists." : "No admin users data found.",
            "data" => [
                "exists" => $adminUsersExist
            ]
        ], 200));
    }
    public function registerAdmin(AdminRegisterRequest $request): void
    {
        $data = $request->validated();

        if (Users::where('username', $data['username'])->count() > 0) {
            // Ada di DB
            throw new HttpResponseException(response([
                "success" => false,
                "message" => "username already registered"
            ], 400));
        }

        $user = new Users($data);
        $user->role = "Admin";
        $user->password = Hash::make($data['password']);
        $user->save();

        throw new HttpResponseException(response([
            "success" => true,
            "message" => "admin registered successfully"
        ], 201));
    }
    public function registerEditor(EditorRegisterRequest $request): void
    {
        $data = $request->validated();

        if (Users::where('username', $data['username'])->count() > 0) {
            // Ada di DB
            throw new HttpResponseException(response([
                "success" => false,
                "message" => "username already registered"
            ], 400));
        }

        if (Users::where('email', $data['email'])->count() > 0) {
            // Ada di DB
            throw new HttpResponseException(response([
                "success" => false,
                "message" => "email already registered"
            ], 400));
        }

        $user = new Users($data);
        $user->role = "Editor";
        $user->password = Hash::make($data['password']);
        $user->save();

        throw new HttpResponseException(response([
            "success" => true,
            "message" => "editor registered successfully"
        ], 201));
    }
    public function register(RegisterRequest $request): void
    {
        $data = $request->validated();

        if (Users::where('username', $data['username'])->count() > 0) {
            // Ada di DB
            throw new HttpResponseException(response([
                "success" => false,
                "message" => "username already registered"
            ], 400));
        }

        $user = new Users($data);
        $user->role = "Reader";
        $user->password = Hash::make($data['password']);
        $user->device_id = $data['device_id'];
        $user->access_token  = Str::random(64);
        $user->access_token_expire = now()->addDays(4);
        $user->save();

        throw new HttpResponseException(response([
            "success" => true,
            "message" => "user registered successfully",
            "data" => [
                "user" => new UserResource($user),
                "access_token" => $user->access_token,
                "access_token_expire" => $user->access_token_expire->format('Y-m-d H:i:s')
            ]
        ], 201));
    }
    public function loginAdminEditor(AdminEditorLoginRequest $request): void
    {
        $data = $request->validated();

        $user = Users::where('username', $data['username'])->first();
        if (!$user) {
            throw new HttpResponseException(response([
                "success" => false,
                "message" => "user not found"
            ], 401));
        }

        if (!in_array($user->role, ['Admin', 'Editor'])) {
            throw new HttpResponseException(response([
                "success" => false,
                "message" => "access denied: role not authorized"
            ], 401));
        }

        if (!Hash::check($data['password'], $user->password)) {
            throw new HttpResponseException(response([
                "success" => false,
                "message" => "password wrong"
            ], 401));
        }

        if ($user->access_token == null || $user->access_token_expire <= now()) {
            $user->access_token  = Str::random(64);
            $user->access_token_expire = now()->addDays(4);
        }

        $user->save();

        throw new HttpResponseException(response([
            "success" => true,
            "data" => [
                "user" => new UserResource($user),
                "access_token" => $user->access_token,
                "access_token_expire" => $user->access_token_expire->format('Y-m-d H:i:s')
            ]
        ], 200));
    }
    public function login(LoginRequest $request): void
    {
        $data = $request->validated();

        $user = Users::where('username', $data['username'])->first();
        if (!$user) {
            throw new HttpResponseException(response([
                "success" => false,
                "message" => "user not found"
            ], 404));
        }

        if (!Hash::check($data['password'], $user->password)) {
            throw new HttpResponseException(response([
                "success" => false,
                "message" => "password wrong"
            ], 401));
        }

        if ($user->role != 'Reader') {
            throw new HttpResponseException(response([
                "success" => false,
                "message" => "access denied: role not authorized"
            ], 401));
        }

        if ($user->device_id == null) {
            $user->device_id = $data['device_id'];
        }

        if ($user->device_id != $data['device_id']) {
            throw new HttpResponseException(response([
                "success" => false,
                "message" => "already logged in on other device"
            ], 403));
        }

        if ($user->access_token == null || $user->access_token_expire <= now()) {
            $user->access_token  = Str::random(64);
            $user->access_token_expire = now()->addDays(4);
        }

        $user->save();

        throw new HttpResponseException(response([
            "success" => true,
            "data" => [
                "user" => new UserResource($user),
                "access_token" => $user->access_token,
                "access_token_expire" => $user->access_token_expire->format('Y-m-d H:i:s')
            ]
        ], 200));
    }
    public function checkLogin(CheckLoginRequest $request)
    {
        $data = $request->validated();

        $userId = $data['user_id'];
        $accessToken = $data['access_token'];

        $user = Users::where('id', $userId)
            ->where('device_id', $data['device_id'])
            ->first();

        $shouldGenerateNewToken = false;

        if (!$user) {
            throw new HttpResponseException(response()->json([
                "success" => false,
                "message" => "user not found"
            ], 404));
        }

        if ($user->access_token === $accessToken && $user->access_token_expire !== null && $user->access_token_expire > now()) {
            // Add
            $user->access_token_expire = now()->addDays(4);
        } else {
            $shouldGenerateNewToken = true;
        }

        if ($shouldGenerateNewToken) {
            $user->access_token = Str::uuid()->toString();
            $user->access_token_expire = now()->addDays(4);
        }

        $user->save();

        throw new HttpResponseException(response([
            "success" => true,
            "data" => [
                "user" => new UserResource($user),
                "access_token" => $user->access_token,
                "access_token_expire" => $user->access_token_expire->format('Y-m-d H:i:s')
            ]
        ], 200));
    }
    public function logout(LogoutRequest $request): void
    {
        $data = $request->validated();

        $user = Users::where('id', $data['user_id'])
            ->where('device_id', $data['device_id'])
            ->first();
        if (!$user) {
            throw new HttpResponseException(response([
                "success" => false,
                "message" => "device id not valid"
            ], 401));
        }

        $user->device_id = null;
        $user->access_token = null;
        $user->access_token_expire = null;
        $user->save();

        throw new HttpResponseException(response([
            "success" => true,
            "message" => "logout success"
        ], 200));
    }
    public function updateDevice(UpdateDeviceRequest $request): void
    {
        $data = $request->validated();

        $user = Users::where('username', $data['username'])
            ->first();

        if (!$user) {
            throw new HttpResponseException(response([
                "success" => false,
                "message" => "user not found"
            ], 404));
        }

        $user->device_id = $data['device_id'];
        $user->access_token  = Str::random(64);
        $user->access_token_expire = now()->addDays(4);

        $user->save();

        throw new HttpResponseException(response([
            "success" => true,
            "data" => [
                "user" => new UserResource($user),
                "access_token" => $user->access_token,
                "access_token_expire" => $user->access_token_expire->format('Y-m-d H:i:s')
            ]
        ], 200));
    }
    public function sendOtp(SendOtpRequest $request): void
    {
        $email = $request->validated('email');

        $user = Users::where('email', $email)->first();

        if (!$user) {
            throw new HttpResponseException(response()->json([
                "success" => false,
                "message" => "User not found."
            ], 404));
        }

        Otp::where('user_id', $user->id)->delete();

        $otpCode = Str::padLeft(random_int(1, 999999), 6, '0');
        $otpExpiresAt = Carbon::now()->addMinutes($this->otpExpiryMinutes);

        Otp::create([
            'user_id' => $user->id,
            'code' => $otpCode,
            'expires_at' => $otpExpiresAt,
        ]);

        Mail::to($user->email)->send(new OtpMail(
            $otpCode,
            $user->username,
            $this->otpExpiryMinutes
        ));

        // Return data yang dibutuhkan JavaScript untuk set cookie
        $response = response()->json([
            "success" => true,
            "message" => "OTP code sent to your email.",
            "data" => [
                "user_id" => $user->id,
                "otp_expire" => $this->otpExpiryMinutes, // Return sebagai integer untuk JS
                "otp_expires_at" => $otpExpiresAt->timestamp,
            ]
        ]);

        throw new HttpResponseException($response);
    }
    public function checkOtp(OtpValidationRequest $request): void
    {
        $data = $request->validated();

        $otp = Otp::where('user_id', $data["user_id"])->first();

        if (!$otp) {
            throw new HttpResponseException(response([
                "success" => false,
                "message" => "OTP Code not found."
            ], 404));
        }

        if ($otp->code != $data["code"]) {
            throw new HttpResponseException(response([
                "success" => false,
                "message" => "Wrong OTP code."
            ], 403));
        }

        if ($otp->expires_at <= now()) {
            throw new HttpResponseException(response([
                "success" => false,
                "message" => "OTP code expired."
            ], 403));
        }

        Otp::where('user_id', $data["user_id"])->delete();

        throw new HttpResponseException(response([
            "success" => true,
            "message" => "OTP validated.",
        ], 200));
    }
    public function updatePw(UpdatePasswordRequest $request): void
    {
        $data = $request->validated();

        $user = Users::where('id', $data["user_id"])->first();
        if (!$user) {
            throw new HttpResponseException(response([
                "success" => false,
                "message" => "User not found."
            ], 404));
        }

        $user->password = Hash::make($data['password']);
        $user->save();

        throw new HttpResponseException(response([
            "success" => true,
            "message" => "Password updated.",
        ], 200));
    }
    public function getUsers(Request $request): void
    {
        $perPage = $request->input('per_page', 10);
        $perPage = max(1, (int)$perPage);

        $currentPage = LengthAwarePaginator::resolveCurrentPage();

        $role = $request->input('role');
        $role = trim($role);

        $cacheKey = 'users_page_' . $currentPage . '_per_page_' . $perPage;
        if (!empty($role)) {
            $cacheKey .= '_role_' . strtolower($role);
        }

        $cacheDuration = 60;

        if ($request->boolean('clear_cache')) {
            Cache::forget($cacheKey);
        }

        $users = Cache::remember($cacheKey, $cacheDuration, function () use ($perPage, $role) {
            $query = Users::query();

            if (!empty($role)) {
                $query->where('role', $role);
            }

            return $query->paginate($perPage);
        });

        throw new HttpResponseException(response([
            "success" => true,
            "message" => "Success get users",
            "data" => new UserCollection($users)
        ], 200));
    }
    public function searchUsers(Request $request): void
    {
        $searchQuery = $request->input('q');
        $searchQuery = trim($searchQuery);

        $roleFilter = $request->input('role');
        $roleFilter = trim($roleFilter);

        $query = Users::query();

        if (!empty($searchQuery)) {
            $query->where(function ($q) use ($searchQuery) {
                $q->where('username', 'like', '%' . $searchQuery . '%')
                    ->orWhere('email', 'like', '%' . $searchQuery . '%')
                    ->orWhere('role', 'like', '%' . $searchQuery . '%');
            });
        }

        if (!empty($roleFilter)) {
            $query->where('role', $roleFilter);
        }

        $users = $query->get();

        throw new HttpResponseException(response([
            "success" => true,
            "message" => "Success search users",
            "data" => new UsersCollectionWithNoPagination($users)
        ], 200));
    }
    public function deleteUser(string $id): void
    {
        if (!is_numeric($id)) {
            throw new HttpResponseException(response([
                "success" => false,
                "message" => "Invalid user ID format. ID must be numeric."
            ], 400));
        }

        $userToDelete = Users::find($id);

        if (!$userToDelete) {
            throw new HttpResponseException(response([
                "success" => false,
                "message" => "User with ID '{$id}' not found."
            ], 404)); // Not Found
        }

        if ($userToDelete->role === 'Reader') {
            throw new HttpResponseException(response([
                "success" => false,
                "message" => "User with role 'Reader' cannot be deleted."
            ], 403)); // Forbidden
        }

        $userToDelete->delete();

        throw new HttpResponseException(response([
            "success" => true,
            "message" => "User '{$userToDelete->username}' (ID: {$userToDelete->id}) deleted successfully."
        ], 200));
    }
}
