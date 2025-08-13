<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    public function initialize(): View|RedirectResponse
    {
        return view('welcome');
    }

    public function showCreateAdmin(): View
    {
        return view('create_admin');
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function showPassword(): View | RedirectResponse
    {
        return view('auth.password');
    }


    public function showOtp(): View | RedirectResponse
    {
        return view('auth.otp');
    }

    public function showGantiPassword(): View | RedirectResponse
    {
        return view('auth.ganti_pw');
    }
}
