<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function admin()
    {
        return view('admin.admin_dashboard');
    }

    public function manager()
    {
        return view('dashboard');
    }

    public function owner()
    {
        return view('dashboard');
    }
}