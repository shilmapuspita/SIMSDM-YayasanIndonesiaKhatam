<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Leave;
use Illuminate\View\View;

class HRDDashboardController extends Controller
{
    /**
     * Display HRD dashboard.
     */
    public function index(): View
    {
        return view('hrd.dashboard', [
            'totalEmployees' => Employee::count(),
            'pendingLeaves'  => Leave::where('status', 'pending')->count(),
        ]);
    }
}