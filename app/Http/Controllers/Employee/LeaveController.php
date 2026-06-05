<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function index()
    {
        $leaves = Leave::where('employee_id', auth()->user()->employee->id)
            ->latest()
            ->get();

        return view('employee.cuti.index', compact('leaves'));
    }

    public function create()
    {
        return view('employee.cuti.create');
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'leave_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'required|string',
        ]);

        Leave::create([
            'employee_id' => $user->employee->id,
            'leave_type'  => $request->leave_type,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'reason'      => $request->reason,
            'status'      => 'pending',
        ]);

        return redirect()->route(auth()->user()->role . '.cuti')
            ->with('success', 'Pengajuan cuti berhasil dikirim');
    }

    public function destroy($id)
    {
        $leave = Leave::where('id', $id)
            ->where('employee_id', Auth::user()->employee->id)
            ->firstOrFail();

        if ($leave->status !== 'pending') {
            return back()->with('error', 'Cuti yang sudah diproses tidak bisa dihapus.');
        }

        $leave->delete();

        return back()->with('success', 'Pengajuan cuti berhasil dihapus.');
    }
}
