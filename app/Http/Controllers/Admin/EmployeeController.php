<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class EmployeeController extends Controller
{
    /* * CATATAN PENTING:
     * Kita tidak pakai __construct middleware di sini.
     * Keamanan Role sudah diatur di routes/web.php (role:admin,hrd).
     */

    // 1. TAMPIL DATA
    public function index()
    {
        $employees = Employee::with('user')
            ->whereHas('user', function ($q) {
                $q->whereIn('role', ['admin', 'hrd', 'employee']);
            })
            ->get();

        return view('karyawan.index', compact('employees'));
    }

    // 2. FORM TAMBAH
    public function create()
    {
        // Cari user yang belum punya data employee
        $users = User::whereDoesntHave('employee')->get();
        return view('karyawan.create', compact('users'));
    }

    // 3. SIMPAN DATA
    public function store(Request $request)
    {
        $request->validate([
            // user
            'email'             => 'required|email|unique:users,email',

            // employee
            'employee_code'     => 'required|unique:employees,employee_code',
            'full_name'         => 'required|string|max:255',
            'gender'            => 'required',
            'birth_date'        => 'required|date',
            'phone'             => 'required',
            'address'           => 'required',
            'position'          => 'required',
            'employment_status' => 'required',
            'join_date'         => 'required|date',
            'jenis_pegawai'     => 'required|in:management,staff,guru,kepsek,kepala_divisi',
        ]);

        /** 1️⃣ BUAT USER LOGIN */
        $user = User::create([
            'name'     => $request->full_name,
            'email'    => $request->email,
            'password' => Hash::make('password123'),
            'role'     => $request->role,
        ]);

        /** 2️⃣ BUAT DATA PEGAWAI */
        Employee::create([
            'user_id'           => $user->id,
            'employee_code'     => $request->employee_code,
            'full_name'         => $request->full_name,
            'gender'            => $request->gender,
            'birth_date'        => $request->birth_date,
            'phone'             => $request->phone,
            'address'           => $request->address,
            'position'          => $request->position,
            'employment_status' => $request->employment_status,
            'join_date'         => $request->join_date,
            'jenis_pegawai'     => $request->jenis_pegawai,
        ]);

        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => 'create_employee',
            'description' => 'User menambahkan pegawai baru',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()
            ->route(auth()->user()->role . '.karyawan.index')
            ->with('success', 'Pegawai berhasil ditambahkan');
    }


    /* * PERHATIKAN DI BAWAH INI:
     * Saya ubah variabel $employee jadi $karyawan.
     * Karena di web.php nama resource-nya 'karyawan', 
     * Laravel akan mencari variabel yang namanya sama.
     */

    // 4. FORM EDIT
    public function edit(Employee $karyawan)
    {
        return view('karyawan.edit', compact('karyawan'));
    }

    // 5. UPDATE DATA
    public function update(Request $request, Employee $karyawan)
    {
        $request->validate([
            'full_name'         => 'required',
            'phone'             => 'required',
            'address'           => 'required',
            'position'          => 'required',
            'employment_status' => 'required',
            'jenis_pegawai'     => 'required|in:management,staff,guru,kepsek,kepala_divisi',
        ]);

        // 1. Update data employee
        $karyawan->update([
            'full_name'         => $request->full_name,
            'phone'             => $request->phone,
            'address'           => $request->address,
            'position'          => $request->position,
            'employment_status' => $request->employment_status,
            'jenis_pegawai'     => $request->jenis_pegawai,
        ]);

        // 2. Update juga data user (nama login)
        if ($karyawan->user) {
            $karyawan->user->update([
                'name' => $request->full_name
            ]);
        }

        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => 'update_employee',
            'description' => 'User mengubah data pegawai',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()
            ->route(auth()->user()->role . '.karyawan.index')
            ->with('success', 'Data pegawai diperbarui');
    }

    // 6. HAPUS
    public function destroy(Employee $karyawan)
    {
        $karyawan->delete();

        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => 'delete_employee',
            'description' => 'User menghapus data pegawai',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);

        return redirect()
            ->route(auth()->user()->role . '.karyawan.index')
            ->with('success', 'Pegawai dihapus');
    }

    // 7. DETAIL
    public function show(Employee $karyawan)
    {
        return view('karyawan.show', compact('karyawan'));
    }
}
