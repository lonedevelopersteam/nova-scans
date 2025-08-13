<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class DashboardController extends Controller
{
    public function showTemplate(): View {
        return view('dashboard');
    }
}
