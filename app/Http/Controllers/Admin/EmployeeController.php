<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /* * CATATAN PENTING:
     * Kita tidak pakai __construct middleware di sini.
     * Keamanan Role sudah diatur di routes/web.php (role:admin,hrd).
     */

    // 1. TAMPIL DATA
    public function index()
    {
        // Mengambil semua data pegawai
        $employees = Employee::with('user')->get(); // Pakai with('user') biar lebih ringan query-nya

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
            'user_id'           => 'required|unique:employees,user_id',
            'employee_code'     => 'required|unique:employees,employee_code',
            'full_name'         => 'required|string|max:255',
            'gender'            => 'required',
            'birth_date'        => 'required|date',
            'phone'             => 'required',
            'address'           => 'required',
            'position'          => 'required',
            'employment_status' => 'required',
            'join_date'         => 'required|date',
        ]);

        Employee::create($request->all());

        return redirect()
            ->route('karyawan.index')
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
        ]);

        $karyawan->update($request->all());

        return redirect()
            ->route('karyawan.index')
            ->with('success', 'Data pegawai diperbarui');
    }

    // 6. HAPUS
    public function destroy(Employee $karyawan)
    {
        $karyawan->delete();

        return redirect()
            ->route('karyawan.index')
            ->with('success', 'Pegawai dihapus');
    }

    // 7. DETAIL
    public function show(Employee $karyawan)
    {
        return view('karyawan.show', compact('karyawan'));
    }
}