<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'leave_type',
        'start_date',
        'end_date',
        'reason',
        'status',
        'approved_by'
    ];

    // Agar tanggal otomatis terbaca sebagai Carbon Date (bisa diformat di blade)
    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    /* ================= RELATIONSHIP ================= */

    /**
     * Relasi ke Pegawai (User).
     * PENTING: Arahkan ke User::class, bukan Employee::class
     * karena foreign key di database mengarah ke tabel users.
     */
    public function user()
    {
        // Parameter 2: 'employee_id' adalah nama kolom di tabel leaves
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    /**
     * Relasi ke Penyetuju (Admin/HRD)
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
