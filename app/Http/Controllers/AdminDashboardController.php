<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\Attendance;
use Illuminate\View\View;
use Carbon\Carbon;

class AdminDashboardController extends Controller
{
    /**
     * Display admin dashboard.
     */
    public function index(): View
    {
        $today = Carbon::today();

        // ===== USER =====
        $totalUsers = User::count();
        $activeUsers = User::where(function ($query) use ($today) {
            $query->where('is_online', true)
                ->orWhere('last_activity_at', '>=', now()->subMinutes(15));
        })->count();

        // ===== EMPLOYEE =====
        $totalEmployees = Employee::count();

        // ===== ADMIN + HRD =====
        $totalHRD = User::whereIn('role', ['admin', 'hrd'])->count();

        // ===== CUTI =====
        $totalCutiPending = Leave::where('status', 'pending')->count();
        $totalCutiApproved = Leave::where('status', 'approved')->count();

        // ===== ABSENSI HARI INI =====
        $hadirHariIni = Attendance::whereDate('attendance_date', $today)->count();
        $tidakHadirHariIni = max($totalEmployees - $hadirHariIni, 0);

        // ===== USER TERBARU =====
        $latestUsers = User::latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalUsers',
            'activeUsers',
            'totalEmployees',
            'totalHRD',
            'totalCutiPending',
            'totalCutiApproved',
            'hadirHariIni',
            'tidakHadirHariIni',
            'latestUsers'
        ));
    }
}
