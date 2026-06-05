<?php

namespace App\Models;

use App\Models\Employee;
use App\Models\User;
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

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    /* ================= RELATIONSHIP ================= */

    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            Employee::class,
            'id',        // employee.id
            'id',        // users.id
            'employee_id',// leaves.employee_id
            'user_id'    // employees.user_id
        );
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }


    /**
     * Relasi ke Penyetuju (Admin/HRD)
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
