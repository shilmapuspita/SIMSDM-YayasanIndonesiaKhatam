<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attendances = Attendance::with('employee')
            ->orderBy('attendance_date', 'desc')
            ->paginate(10);

        return view('absensi.index', compact('attendances'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $employees = Employee::orderBy('full_name')->get();

        return view('absensi.create', compact('employees'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => [
                'required',
                'exists:employees,id',
                // Validasi Unik Kombinasi: Cek apakah ID ini sudah absen di tanggal yang sama
                Rule::unique('attendances')->where(function ($query) use ($request) {
                    return $query->where('attendance_date', $request->attendance_date);
                }),
            ],
            'attendance_date' => 'required|date',
            'check_in'        => 'nullable',
            'check_out'       => 'nullable|after:check_in',
            'status'          => 'required|in:present,leave,sick,absent',
        ], [
            'employee_id.unique' => 'Pegawai ini sudah memiliki data absensi pada tanggal tersebut.',
        ]);

        Attendance::create($request->all());

        return redirect()
            ->route('absensi.index')
            ->with('success', 'Data absensi berhasil ditambahkan');
    }

    /**
     * Display the specified resource.
     */
    public function show(Attendance $attendance)
    {
        $attendance->load('employee');

        return view('absensi.show', compact('attendance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Attendance $attendance)
    {
        $employees = Employee::orderBy('full_name')->get();

        return view('absensi.edit', compact('attendance', 'employees'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Attendance $attendance)
    {
        $validated = $request->validate([
            'employee_id'     => 'required|exists:employees,id',
            'attendance_date' => 'required|date',
            'check_in'        => 'nullable',
            'check_out'       => 'nullable|after:check_in',
            'status'          => 'required|in:present,leave,sick,absent',
        ]);

        $attendance->update($validated);

        return redirect()
            ->route('absensi.index')
            ->with('success', 'Data absensi berhasil diperbarui');
    }

    public function destroy($id)
    {
        $attendance = Attendance::findOrFail($id);
        
        $attendance->delete();

        // 3. Kembali ke halaman sebelumnya dengan pesan sukses
        return redirect()->back()
            ->with('success', 'Data absensi berhasil dihapus');
    }
}
