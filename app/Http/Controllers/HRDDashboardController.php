<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Leave;
use App\Models\Attendance;
use Illuminate\View\View;
use Carbon\Carbon;

class HRDDashboardController extends Controller
{
    /**
     * Display HRD dashboard.
     */
    public function index(): View
    {
        $today = Carbon::today();
        $weekStart = Carbon::now()->startOfWeek();
        $weekEnd = Carbon::now()->endOfWeek();

        // Statistik Utama
        $totalEmployees = Employee::count();
        $pendingLeaves = Leave::where('status', 'pending')->count();
        $todayAttendance = Attendance::whereDate('attendance_date', $today)->where('status', 'present')->count();
        $lateToday = Attendance::whereDate('attendance_date', $today)->where('check_in', '>', '09:00:00')->count();

        // Data Cuti Terbaru
        $latestLeaves = Leave::with('employee', 'user')
            ->latest()
            ->take(5)
            ->get();

        // Data untuk Chart Mingguan (7 hari terakhir)
        $startDate = Carbon::today()->subDays(6);
        $attendanceSummary = Attendance::selectRaw('attendance_date, status, count(*) as total')
            ->whereBetween('attendance_date', [$startDate, $today])
            ->groupBy('attendance_date', 'status')
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->attendance_date)->format('Y-m-d');
            });

        $attendanceTrendLabels = [];
        $attendanceTrendPresent = [];
        $attendanceTrendLate = [];
        $attendanceTrendLeave = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $key = $date->format('Y-m-d');
            $attendanceTrendLabels[] = $date->format('d M');

            $dailyRecords = $attendanceSummary->get($key, collect());
            $present = $dailyRecords->sum(function ($row) {
                return in_array($row->status, ['present', 'late']) ? $row->total : 0;
            });
            $late = $dailyRecords->sum(function ($row) {
                return $row->status === 'late' ? $row->total : 0;
            });
            $leave = $dailyRecords->sum(function ($row) {
                return in_array($row->status, ['leave', 'sick']) ? $row->total : 0;
            });

            $attendanceTrendPresent[] = $present;
            $attendanceTrendLate[] = $late;
            $attendanceTrendLeave[] = $leave;
        }

        return view('hrd.dashboard', [
            'totalEmployees' => $totalEmployees,
            'pendingLeaves' => $pendingLeaves,
            'todayAttendance' => $todayAttendance,
            'lateToday' => $lateToday,
            'latestLeaves' => $latestLeaves,
            'attendanceTrendLabels' => $attendanceTrendLabels,
            'attendanceTrendPresent' => $attendanceTrendPresent,
            'attendanceTrendLate' => $attendanceTrendLate,
            'attendanceTrendLeave' => $attendanceTrendLeave,
        ]);
    }
}
