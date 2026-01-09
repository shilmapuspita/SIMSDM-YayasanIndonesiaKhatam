<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Display admin dashboard.
     */
    public function index(): View
    {
        return view('admin.dashboard', [
            'totalUsers'     => User::count(),
            'totalEmployees' => Employee::count(),
        ]);
    }
}