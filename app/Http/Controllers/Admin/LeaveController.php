<?php

namespace App\Http\Controllers\Admin;


use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\Leave;
use App\Models\User;
use Illuminate\Http\Request;


class LeaveController extends Controller
{
    public function index()
    {
        // Kita load relasi 'user.employee' biar bisa ambil full_name
        $leaves = Leave::with(['user.employee', 'approver'])
            ->latest()
            ->paginate(10);

        return view('cuti.index', compact('leaves'));
    }

    // FORM TAMBAH CUTI
    public function create()
    {
        // Ambil User yang role-nya employee
        // Load relasi 'employee' agar kita bisa ambil 'full_name' & 'employee_code'
        $employees = User::with('employee')
            ->where('role', 'employee')
            ->get();

        return view('cuti.create', compact('employees'));
    }

    // SIMPAN DATA
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:users,id', // Validasi ke tabel USERS
            'leave_type'  => 'required|string',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'reason'      => 'required|string',
            'status'      => 'required|in:pending,approved,rejected',
        ]);

        Leave::create([
            'employee_id' => $request->employee_id, // Ini nyimpen ID User
            'leave_type'  => $request->leave_type,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'reason'      => $request->reason,
            'status'      => $request->status,
            'approved_by' => $request->status === 'approved' ? Auth::id() : null,
        ]);

        return redirect()->route('cuti.index')->with('success', 'Data cuti berhasil disimpan.');
    }

    /**
     * Menampilkan detail cuti (Show)
     */
    public function show($id)
    {
        $leave = Leave::with('user')->findOrFail($id);
        return view('cuti.show', compact('leave'));
    }

    /**
     * Menampilkan form edit (Edit)
     */
    public function edit($id)
    {
        $leave = Leave::findOrFail($id);
        $employees = User::where('role', 'employee')->get();

        return view('cuti.edit', compact('leave', 'employees'));
    }

    /**
     * Menyimpan perubahan data (Update)
     */
    public function update(Request $request, $id)
    {
        // 1. VALIDASI
        $request->validate([
            'employee_id' => 'required|exists:users,id', // Penting: Biar bisa ganti pegawai
            'leave_type'  => 'required|string',          // Koreksi: Pakai 'leave_type' bukan 'type'
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'reason'      => 'required|string|max:255',
            'status'      => 'required|in:pending,approved,rejected',
        ]);

        // 2. AMBIL DATA LAMA
        $leave = Leave::findOrFail($id);

        // 3. UPDATE DATA
        // Kita set manual satu-satu biar aman dan bisa masukin logika 'approved_by'
        $leave->update([
            'employee_id' => $request->employee_id,
            'leave_type'  => $request->leave_type,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'reason'      => $request->reason,
            'status'      => $request->status,

            'approved_by' => $request->status === 'approved' ? Auth::id() : $leave->approved_by,
        ]);

        // 4. REDIRECT (Ini sudah benar kodenya)
        return redirect()->route('cuti.index')
            ->with('success', 'Data cuti berhasil diperbarui.');
    }

    /**
     * Menghapus data (Destroy)
     */
    public function destroy($id)
    {
        $leave = Leave::findOrFail($id);
        $leave->delete();

        return redirect()->route('cuti.index')->with('success', 'Data cuti berhasil dihapus.');
    }
}
