<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\AttendanceExport;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf as PDF;

class AttendanceController extends Controller
{
    /* ========================= QUERY FILTER ========================= */
    protected function attendanceQuery(Request $request)
    {
        $query = Attendance::with('employee')->orderBy('attendance_date', 'desc');

        if ($request->filled('date')) {
            $query->whereDate('attendance_date', $request->date);
        }

        if ($request->filled('month')) {
            $query->whereMonth('attendance_date', $request->month);
        }

        if ($request->filled('year')) {
            $query->whereYear('attendance_date', $request->year);
        }

        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        return $query;
    }

    /* ========================= INDEX ========================= */
    public function index(Request $request)
    {
        $attendances = $this->attendanceQuery($request)
            ->paginate(10)
            ->withQueryString();

        $employees = Employee::orderBy('full_name')->get();
        $years = Attendance::selectRaw('YEAR(attendance_date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('absensi.index', compact('attendances', 'employees', 'years'));
    }

    /* ========================= EXPORT ========================= */
    public function exportExcel(Request $request)
    {
        return Excel::download(new AttendanceExport($request->all()), 'rekap-absensi.xlsx');
    }

    public function exportPdf(Request $request)
    {
        $attendances = $this->attendanceQuery($request)->get();

        $pdf = PDF::loadView('absensi.pdf', compact('attendances'))
            ->setPaper('a4', 'landscape');

        return $pdf->download('rekap-absensi.pdf');
    }

    /* ========================= FORM CREATE ========================= */
    public function create()
    {
        $employees = Employee::orderBy('full_name')->get();
        return view('absensi.create', compact('employees'));
    }

    /* ========================= CHECK IN ========================= */
    public function checkIn()
    {
        $user = auth()->user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();

        $today = now()->toDateString();

        // 🚫 CEK SUDAH ABSEN BELUM
        $already = Attendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $today)
            ->first();

        if ($already) {
            return back()->with('error', 'Kamu sudah check-in hari ini');
        }

        // ⏰ LOGIC TERLAMBAT (jam 08:00)
        $status = now()->format('H:i:s') > '08:00:00' ? 'late' : 'present';

        Attendance::create([
            'employee_id'     => $employee->id,
            'attendance_date' => $today,
            'check_in'        => now(),
            'status'          => $status,
        ]);

        return back()->with('success', 'Berhasil check-in');
    }

    /* ========================= CHECK OUT ========================= */
    public function checkOut()
    {
        $user = auth()->user();
        $employee = Employee::where('user_id', $user->id)->firstOrFail();

        $today = now()->toDateString();

        // 🔥 AMBIL DATA HARI INI YANG BELUM CHECKOUT
        $attendance = Attendance::where('employee_id', $employee->id)
            ->whereDate('attendance_date', $today)
            ->whereNull('check_out')
            ->latest()
            ->first();

        if (!$attendance) {
            return back()->with('error', 'Kamu belum check-in atau sudah check-out');
        }

        $attendance->update([
            'check_out' => now(),
        ]);

        return back()->with('success', 'Berhasil check-out');
    }

    /* ========================= STORE MANUAL ========================= */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => [
                'required',
                'exists:employees,id',
                Rule::unique('attendances')->where(function ($query) use ($request) {
                    return $query->where('attendance_date', $request->attendance_date);
                }),
            ],
            'attendance_date' => 'required|date',
            'check_in'        => 'nullable',
            'check_out'       => 'nullable|after:check_in',
            'status'          => 'required|in:present,late,leave,sick,absent',
        ]);

        Attendance::create($request->all());

        return redirect()->route(auth()->user()->role . '.absensi.index')
            ->with('success', 'Data absensi berhasil ditambahkan');
    }

    /* ========================= SHOW ========================= */
    public function show(Attendance $attendance)
    {
        $attendance->load(['employee.user']);
        return view('absensi.show', compact('attendance'));
    }

    /* ========================= EDIT ========================= */
    public function edit(Attendance $absensi)
    {
        $employees = Employee::orderBy('full_name')->get();
        return view('absensi.edit', compact('absensi', 'employees'));
    }

    /* ========================= UPDATE ========================= */
    public function update(Request $request, Attendance $absensi)
    {
        $validated = $request->validate([
            'employee_id'     => 'required|exists:employees,id',
            'attendance_date' => 'required|date',
            'check_in'        => 'nullable',
            'check_out'       => 'nullable|after:check_in',
            'status'          => 'required|in:present,late,leave,sick,absent',
        ]);

        $absensi->update($validated);

        return redirect()->route(auth()->user()->role . '.absensi.index')
            ->with('success', 'Data absensi berhasil diperbarui');
    }

    /* ========================= DELETE ========================= */
    public function destroy($id)
    {
        Attendance::findOrFail($id)->delete();

        return redirect()->route(auth()->user()->role . '.absensi.index')
            ->with('success', 'Data absensi berhasil dihapus');
    }
}
