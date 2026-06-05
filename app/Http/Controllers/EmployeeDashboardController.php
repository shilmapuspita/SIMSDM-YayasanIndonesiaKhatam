<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Models\Attendance;
use App\Models\Employee;
use Carbon\Carbon;
use App\Models\Leave;
use App\Helpers\LocationHelper;

class EmployeeDashboardController extends Controller
{
    /**
     * Dashboard employee
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            abort(403, 'Akun Anda belum terdaftar di tabel Employees. Hubungi HR.');
        }

        $today = Carbon::today();
        $monthStart = Carbon::now()->startOfMonth();

        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $today)
            ->first();

        $attendancesThisMonth = Attendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', '>=', $monthStart)
            ->whereDate('attendance_date', '<=', $today)
            ->get();

        $presentDays = $attendancesThisMonth->whereIn('status', ['present', 'late'])->count();
        $lateDays = $attendancesThisMonth->where('status', 'late')->count();

        $pendingLeaves = Leave::where('employee_id', $employee->id)
            ->where('status', 'pending')
            ->count();

        $approvedLeaves = Leave::where('employee_id', $employee->id)
            ->where('status', 'approved')
            ->count();

        $leaveQuota = 12;
        $remainingLeave = max($leaveQuota - $approvedLeaves, 0);

        $chartLabels = [];
        $chartData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $chartLabels[] = $date->format('d M');
            $chartData[] = Attendance::where('employee_id', $employee->id)
                ->whereDate('attendance_date', $date)
                ->whereIn('status', ['present', 'late'])
                ->count();
        }

        return view('employee.dashboard', [
            'todayAttendance' => $todayAttendance,
            'presentDays' => $presentDays,
            'lateDays' => $lateDays,
            'pendingLeaves' => $pendingLeaves,
            'approvedLeaves' => $approvedLeaves,
            'remainingLeave' => $remainingLeave,
            'leaveQuota' => $leaveQuota,
            'chartLabels' => json_encode($chartLabels),
            'chartData' => json_encode($chartData),
        ]);
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
            'hadir' => $history->whereIn('status', ['present', 'late'])->count(),
            'terlambat' => $history->where('status', 'late')->count(),
        ];

        return view('employee.absensi', compact('todayAttendance', 'history', 'stats'));
    }

    // === 3. PROSES ABSEN MASUK ===
    public function storeAbsensi(Request $request)
    {
        // Validasi input dari frontend
        try {
            $validated = $request->validate([
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
                'attendance_type' => 'required|in:check_in,check_out',
            ], [
                'latitude.required' => 'Lokasi tidak terdeteksi. Pastikan izin lokasi sudah diberikan.',
                'longitude.required' => 'Lokasi tidak terdeteksi. Pastikan izin lokasi sudah diberikan.',
                'latitude.numeric' => 'Koordinat tidak valid.',
                'longitude.numeric' => 'Koordinat tidak valid.',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first() ?? 'Validasi gagal.',
                'errors' => $e->errors(),
            ], 422);
        }

        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Akun belum terhubung ke data pegawai. Hubungi HR.',
            ], 403);
        }

        // Validasi koordinat user
        if (!LocationHelper::isValidCoordinate($validated['latitude'], $validated['longitude'])) {
            return response()->json([
                'success' => false,
                'message' => 'Koordinat tidak valid.',
            ], 422);
        }

        // Ambil koordinat kantor dari config
        $officeLat = (float) config('attendance.office_latitude');
        $officeLon = (float) config('attendance.office_longitude');
        $officeRadius = (int) config('attendance.office_radius');

        // Hitung jarak user dengan kantor
        $userLat = (float) $validated['latitude'];
        $userLon = (float) $validated['longitude'];
        $distance = LocationHelper::haversineDistance($userLat, $userLon, $officeLat, $officeLon);

        // Validasi apakah user dalam radius kantor
        if ($distance > $officeRadius) {
            return response()->json([
                'success' => false,
                'message' => sprintf(
                    'Anda berada %.0f meter dari kantor. Absensi hanya dapat dilakukan dalam radius %d meter dari kantor.',
                    $distance,
                    $officeRadius
                ),
                'distance' => $distance,
                'is_within_radius' => false,
            ], 422);
        }

        $today = Carbon::today();
        $now = Carbon::now();

        // Cek apakah sudah ada record hari ini
        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $today)
            ->first();

        if (!$attendance) {
            // Dapatkan jam kerja berdasarkan jenis_pegawai
            $workingHours = \App\Helpers\StatusHelper::getWorkingHours($employee->jenis_pegawai);
            $checkInTime = $workingHours['check_in']; // Format: HH:MM

            // Buat record baru jika belum ada
            // Status: late jika check_in > dari batas jam kerja
            $status = $now->toTimeString() > $checkInTime . ':00' ? 'late' : 'present';

            $attendance = Attendance::create([
                'employee_id' => $employee->id,
                'attendance_date' => $today,
                'check_in' => $now->toTimeString(),
                'check_in_latitude' => $userLat,
                'check_in_longitude' => $userLon,
                'check_in_distance' => round($distance, 2),
                'status' => $status,
            ]);

            ActivityLog::create([
                'user_id' => $user->id,
                'activity' => 'check_in',
                'description' => 'User melakukan absen masuk',
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return response()->json([
                'success' => true,
                'message' => sprintf('Berhasil absen masuk! Anda %.0f meter dari kantor.', $distance),
                'attendance_type' => 'check_in',
                'distance' => $distance,
            ]);
        }

        if ($attendance->check_out) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absen pulang hari ini. Hubungi admin jika ada kesalahan.',
            ], 422);
        }

        // Jika sudah ada check_in tapi belum check_out, anggap ini check_out
        if ($attendance->check_in && !$attendance->check_out) {
            $attendance->update([
                'check_out' => $now->toTimeString(),
                'check_out_latitude' => $userLat,
                'check_out_longitude' => $userLon,
                'check_out_distance' => round($distance, 2),
            ]);

            return response()->json([
                'success' => true,
                'message' => sprintf('Berhasil absen pulang! Anda %.0f meter dari kantor.', $distance),
                'attendance_type' => 'check_out',
                'distance' => $distance,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Anda sudah melakukan absen masuk hari ini. Tunggu untuk absen pulang.',
        ], 422);
    }

    // === 4. PROSES ABSEN PULANG (via updateAbsensi) ===
    public function updateAbsensi(Request $request)
    {
        // Validasi input dari frontend
        try {
            $validated = $request->validate([
                'latitude' => 'required|numeric|between:-90,90',
                'longitude' => 'required|numeric|between:-180,180',
            ], [
                'latitude.required' => 'Lokasi tidak terdeteksi. Pastikan izin lokasi sudah diberikan.',
                'longitude.required' => 'Lokasi tidak terdeteksi. Pastikan izin lokasi sudah diberikan.',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => collect($e->errors())->flatten()->first() ?? 'Validasi gagal.',
                'errors' => $e->errors(),
            ], 422);
        }

        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Akun belum terhubung ke data pegawai. Hubungi HR.',
            ], 403);
        }

        // Validasi koordinat user
        if (!LocationHelper::isValidCoordinate($validated['latitude'], $validated['longitude'])) {
            return response()->json([
                'success' => false,
                'message' => 'Koordinat tidak valid.',
            ], 422);
        }

        // Ambil koordinat kantor dari config
        $officeLat = (float) config('attendance.office_latitude');
        $officeLon = (float) config('attendance.office_longitude');
        $officeRadius = (int) config('attendance.office_radius');

        // Hitung jarak user dengan kantor
        $userLat = (float) $validated['latitude'];
        $userLon = (float) $validated['longitude'];
        $distance = LocationHelper::haversineDistance($userLat, $userLon, $officeLat, $officeLon);

        // Validasi apakah user dalam radius kantor
        if ($distance > $officeRadius) {
            return response()->json([
                'success' => false,
                'message' => sprintf(
                    'Anda berada %.0f meter dari kantor. Absensi hanya dapat dilakukan dalam radius %d meter dari kantor.',
                    $distance,
                    $officeRadius
                ),
                'distance' => $distance,
                'is_within_radius' => false,
            ], 422);
        }

        $today = Carbon::today();
        $now = Carbon::now();

        // Cek apakah sudah ada record check_in hari ini
        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $today)
            ->first();

        if (!$attendance) {
            return response()->json([
                'success' => false,
                'message' => 'Anda belum melakukan absen masuk hari ini.',
            ], 422);
        }

        if ($attendance->check_out) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah melakukan absen pulang hari ini.',
            ], 422);
        }

        // Update check_out
        $attendance->update([
            'check_out' => $now->toTimeString(),
            'check_out_latitude' => $userLat,
            'check_out_longitude' => $userLon,
            'check_out_distance' => round($distance, 2),
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'activity' => 'check_out',
            'description' => 'User melakukan absen pulang',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => sprintf('Berhasil absen pulang! Anda %.0f meter dari kantor.', $distance),
            'attendance_type' => 'check_out',
            'distance' => $distance,
        ]);
    }

    /**
     * Halaman cuti employee
     */
    public function cuti(Request $request)
    {
        $user = Auth::user();
        $employee = $user->employee; // Ambil data employee dari user yg login

        // Ambil parameter type dari query string untuk pre-select form dan filter riwayat
        $selectedType = $request->query('type', null);

        // Query Cari data berdasarkan employee_id
        $query = Leave::where('employee_id', $employee->id);

        // Jika selectedType ada, filter riwayat untuk menampilkan hanya jenis cuti yang dipilih
        if ($selectedType) {
            $query->where('leave_type', $selectedType);
        }

        $leaves = $query->orderBy('created_at', 'desc')->get();

        return view('employee.cuti', compact('leaves', 'selectedType'));
    }
    public function storeCuti(Request $request)
    {
        $request->validate([
            'leave_type' => 'required|in:annual,sick,permit',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'required|string',
            'attachment' => 'nullable|file|max:2048',
        ]);

        $user = Auth::user();
        $employee = $user->employee;

        if (!$employee) {
            return back()->withErrors(['Akun Anda belum terdaftar sebagai pegawai.']);
        }

        $startDate = Carbon::parse($request->start_date)->format('Y-m-d');
        $endDate = Carbon::parse($request->end_date)->format('Y-m-d');
        $duration = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;

        // Validasi saldo cuti tahunan
        if ($request->leave_type === 'annual') {
            // Hitung total cuti tahunan yang sudah APPROVED tahun ini
            $approvedThisYear = Leave::where('employee_id', $employee->id)
                ->where('leave_type', 'annual')
                ->where('status', 'approved')
                ->whereYear('created_at', now()->year)
                ->sum('jumlah_hari');

            // Hitung total cuti tahunan yang masih PENDING tahun ini
            $pendingThisYear = Leave::where('employee_id', $employee->id)
                ->where('leave_type', 'annual')
                ->where('status', 'pending')
                ->whereYear('created_at', now()->year)
                ->sum('jumlah_hari');

            // Saldo efektif = 12 - approved - pending
            $availableBalance = 12 - $approvedThisYear - $pendingThisYear;

            if ($duration > $availableBalance) {
                return back()->withErrors([
                    "Saldo cuti tahunan tidak mencukupi. Sisa saldo Anda adalah {$availableBalance} hari."
                ]);
            }
        }

        $exists = Leave::where('employee_id', $employee->id)
            ->whereIn('status', ['pending', 'approved'])
            ->where(function ($q) use ($startDate, $endDate) {
                $q->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q2) use ($startDate, $endDate) {
                        $q2->where('start_date', '<', $startDate)
                            ->where('end_date', '>', $endDate);
                    });
            })
            ->exists();

        if ($exists) {
            return back()->withErrors(['Tanggal cuti bentrok dengan pengajuan lain']);
        }

        $filePath = null;
        if ($request->hasFile('attachment') && in_array($request->leave_type, ['sick', 'permit'])) {
            $filePath = $request->file('attachment')->store('cuti', 'public');
        }

        $leave = new Leave();
        $leave->employee_id = $employee->id;
        $leave->leave_type = $request->leave_type;
        $leave->start_date = $startDate;
        $leave->end_date = $endDate;
        $leave->reason = $request->reason;
        $leave->attachment = $filePath;
        $leave->status = 'pending';
        $leave->jumlah_hari = $duration;
        $leave->save();

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
