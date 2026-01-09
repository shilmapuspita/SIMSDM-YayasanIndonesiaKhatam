<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\Attendance;
use Carbon\Carbon;
use App\Models\Leave;

class EmployeeDashboardController extends Controller
{
    /**
     * Dashboard employee
     */
    public function index(Request $request): View
    {
        return view('employee.dashboard');
    }

    /**
     * Halaman absensi employee
     */
    // === 2. HALAMAN ABSENSI ===
    public function absensi(Request $request): View
    {
        $user = Auth::user();

        // AMBIL DATA EMPLOYEE MILIK USER INI
        $employee = $user->employee;

        // Kalau User ini belum disetting sebagai Employee di database, tolak.
        if (!$employee) {
            abort(403, 'Akun Anda belum terdaftar di tabel Employees. Hubungi HR.');
        }

        $today = Carbon::today();

        // Cek data absen pakai ID EMPLOYEE, bukan ID USER
        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $today)
            ->first();

        $history = Attendance::where('employee_id', $employee->id)
            ->whereMonth('attendance_date', $today->month)
            ->orderBy('attendance_date', 'desc')
            ->get();

        $stats = [
            'hadir' => $history->where('status', 'present')->count(),
            'terlambat' => $history->where('status', 'present')
                ->where('check_in', '>', '08:00:00')
                ->count(),
        ];

        return view('employee.absensi', compact('todayAttendance', 'history', 'stats'));
    }

    // === 3. PROSES ABSEN MASUK ===
    public function storeAbsensi(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return back()->with('error', 'Data Employee tidak ditemukan untuk akun ini.');
        }

        $today = Carbon::today();
        $now = Carbon::now();

        // Cek Double pakai employee->id
        $cek = Attendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $today)
            ->first();

        if ($cek) {
            return back()->with('error', 'Anda sudah melakukan absen masuk hari ini!');
        }

        Attendance::create([
            'employee_id' => $employee->id,
            'attendance_date' => $today,
            'check_in' => $now->toTimeString(),
            'status' => 'present',
        ]);

        return back()->with('success', 'Berhasil Absen Masuk!');
    }

    // === 4. PROSES ABSEN PULANG ===
    public function updateAbsensi()
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return back()->with('error', 'Data Employee tidak ditemukan.');
        }

        $today = Carbon::today();

        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $today)
            ->first();

        if (!$attendance) {
            return back()->with('error', 'Anda belum absen masuk.');
        }

        $attendance->update([
            'check_out' => Carbon::now()->toTimeString()
        ]);

        return back()->with('success', 'Absen pulang tercatat.');
    }

    /**
     * Halaman cuti employee
     */
    public function cuti()
    {
        $user = Auth::user();
        $employee = $user->employee; // Ambil data employee dari user yg login

        // Query Cari data berdasarkan employee_id
        $leaves = Leave::where('employee_id', $employee->id) // <--- PENTING: Pakai employee->id
            ->orderBy('created_at', 'desc')
            ->get();

        return view('employee.cuti', compact('leaves'));
    }
    public function storeCuti(Request $request)
    {
        // 1. Validasi input name="leave_type"
        $request->validate([
            'leave_type' => 'required',
            'start_date' => 'required|date',
            'end_date'   => 'required|date',
            'reason'     => 'required',
        ]);

        $user = Auth::user();
        $employee = $user->employee;

        // 2. Simpan ke Database
        Leave::create([
            'employee_id' => $employee->id,
            'leave_type'  => $request->leave_type, // <--- Pastikan KIRI 'leave_type', KANAN $request->leave_type
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'reason'      => $request->reason,
            'status'      => 'pending',
        ]);

        return back()->with('success', 'Berhasil diajukan!');
    }

    /**
     * Profil employee
     */
    public function profile(Request $request): View
    {
        return view('employee.profile');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $request->user()->id,
        ]);

        $user = $request->user();
        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return redirect()->route('employee.profile')->with('success', 'Profil berhasil diperbarui.');
    }
}
