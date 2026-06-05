<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Leave;
use App\Models\User;
use App\Models\Employee;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class LeaveController extends Controller
{
    /**
     * LIST DATA CUTI (ADMIN / HRD)
     */
    public function index(Request $request)
    {
        $query = Leave::with(['employee.user', 'approver'])->latest();

        // Filter berdasarkan bulan
        if ($request->filled('month')) {
            $query->whereMonth('created_at', $request->month);
        }

        // Filter berdasarkan tahun
        if ($request->filled('year')) {
            $query->whereYear('created_at', $request->year);
        }

        // Filter berdasarkan pegawai
        if ($request->filled('employee_id')) {
            $query->where('employee_id', $request->employee_id);
        }

        $leaves = $query->paginate(10);

        // Ambil data untuk dropdown filter
        $employees = Employee::orderBy('full_name')->get();
        $years = Leave::selectRaw('YEAR(created_at) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year');

        return view('cuti.index', compact('leaves', 'employees', 'years'));
    }

    /**
     * FORM BUAT PENGAJUAN CUTI (ADMIN / HRD)
     */
    public function create(Request $request)
    {
        // Ambil type dari query parameter (annual, sick, permit)
        $selectedType = $request->query('type', 'annual');

        // Ambil riwayat cuti pribadi user yang login, filtered by leave_type
        $user = Auth::user();
        $employee = $user->employee;

        $leaves = Leave::where('employee_id', $employee->id)
            ->where('leave_type', $selectedType)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('cuti.create', compact('selectedType', 'leaves'));
    }

    /**
     * SIMPAN PENGAJUAN CUTI (SEMUA ROLE)
     */
    public function store(Request $request)
    {
        $request->validate([
            'leave_type' => 'required|in:annual,sick,permit',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'reason'     => 'required|string',
            'attachment' => 'nullable|file|max:2048'
        ]);

        $employee = Employee::where('user_id', Auth::id())->first();

        if (!$employee) {
            return back()->withErrors(['User tidak memiliki data employee']);
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

        // 🔥 CEK CUTI BENTROK
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

        return redirect()
            ->back()
            ->with('success', 'Pengajuan cuti berhasil dikirim');
    }

    /**
     * DETAIL CUTI
     */
    public function show($id)
    {
        $leave = Leave::with(['employee.user', 'approver'])
            ->findOrFail($id);

        return view('cuti.show', compact('leave'));
    }

    /**
     * EDIT CUTI
     */
    public function edit($id)
    {
        if (Auth::user()->role !== 'hrd') {
            abort(403, 'Hanya HRD yang bisa mengedit cuti');
        }

        $leave = Leave::with(['employee.user'])->findOrFail($id);
        $employees = User::with('employee')->whereHas('employee')->get();

        return view('cuti.edit', compact('leave', 'employees'));
    }

    /**
     * UPDATE (EDIT / APPROVE / REJECT OLEH HRD)
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->role !== 'hrd') {
            abort(403, 'Hanya HRD yang bisa memproses cuti');
        }

        $leave = Leave::with('employee')->findOrFail($id);

        // Persetujuan / penolakan
        if ($request->filled('status') && in_array($request->status, ['approved', 'rejected'])) {
            if ($leave->employee->user_id == Auth::id()) {
                return back()->withErrors(['Tidak bisa memproses cuti sendiri']);
            }

            $request->validate([
                'status' => 'required|in:approved,rejected',
            ]);

            if ($leave->status === 'pending') {
                $approvedDays = $leave->jumlah_hari ?? Carbon::parse($leave->start_date)->diffInDays(Carbon::parse($leave->end_date)) + 1;

                if ($request->status === 'approved' && $leave->leave_type === 'annual') {
                    if ($leave->employee->annual_leave_balance < $approvedDays) {
                        return back()->withErrors(['Saldo cuti tahunan tidak mencukupi untuk menyetujui pengajuan ini']);
                    }
                }

                if ($request->status === 'approved' && $leave->leave_type === 'annual') {
                    $leave->employee->annual_leave_balance -= $approvedDays;
                    if ($leave->employee->annual_leave_balance < 0) {
                        return back()->withErrors(['Saldo cuti tahunan tidak boleh negatif']);
                    }
                    $leave->employee->save();
                }

                $data = [
                    'status' => $request->status,
                    'approved_by' => Auth::id(),
                ];

                if (Schema::hasColumn('leaves', 'approved_at')) {
                    $data['approved_at'] = now();
                }

                $leave->update($data);
            } elseif ($leave->status === 'approved' && $request->status === 'rejected') {
                $approvedDays = $leave->jumlah_hari ?? Carbon::parse($leave->start_date)->diffInDays(Carbon::parse($leave->end_date)) + 1;

                if ($leave->leave_type === 'annual') {
                    $leave->employee->annual_leave_balance += $approvedDays;
                    $leave->employee->save();
                }

                $data = [
                    'status' => 'rejected',
                    'approved_by' => Auth::id(),
                ];

                if (Schema::hasColumn('leaves', 'approved_at')) {
                    $data['approved_at'] = now();
                }

                $leave->update($data);
            } else {
                return back()->withErrors(['Cuti sudah diproses']);
            }

            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => $request->status === 'approved' ? 'approve_leave' : 'reject_leave',
                'description' => sprintf('HRD %s pengajuan cuti %s', $request->status === 'approved' ? 'menyetujui' : 'menolak', $leave->employee->full_name ?? 'pegawai'),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            return redirect()->route(auth()->user()->role . '.cuti.index')
                ->with('success', 'Cuti berhasil diproses');
        }

        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'leave_type'  => 'required|in:annual,sick,maternity,permit',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'reason'      => 'required|string',
            'status'      => 'required|in:pending,approved,rejected',
        ]);

        if ($leave->status !== 'pending') {
            return back()->withErrors(['Cuti yang sudah diproses tidak bisa diubah']);
        }

        $leave->update([
            'employee_id' => $request->employee_id,
            'leave_type'  => $request->leave_type,
            'start_date'  => $request->start_date,
            'end_date'    => $request->end_date,
            'reason'      => $request->reason,
            'status'      => $request->status,
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'update_leave',
            'description' => sprintf('HRD mengubah pengajuan cuti %s', $leave->employee->full_name ?? 'pegawai'),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return redirect()->route(auth()->user()->role . '.cuti.index')
            ->with('success', 'Data pengajuan cuti berhasil diperbarui');
    }

    /**
     * HAPUS CUTI (HANYA PENDING)
     */
    public function destroy($id)
    {
        if (Auth::user()->role !== 'hrd') {
            abort(403, 'Hanya HRD yang bisa menghapus cuti');
        }

        $leave = Leave::with('employee')->findOrFail($id);

        if ($leave->status !== 'pending') {
            return back()->withErrors(['Cuti yang sudah diproses tidak bisa dihapus']);
        }

        $leave->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity' => 'delete_leave',
            'description' => sprintf('HRD menghapus pengajuan cuti %s', $leave->employee->full_name ?? 'pegawai'),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()->route(auth()->user()->role . '.cuti.index')
            ->with('success', 'Pengajuan cuti berhasil dihapus');
    }
}
