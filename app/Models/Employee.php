<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'employee_code',
        'full_name',
        'gender',
        'birth_date',
        'phone',
        'address',
        'position',
        'employment_status',
        'join_date'
    ];

    /* ================= RELATIONSHIP ================= */

    // employee milik satu user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // employee punya banyak absensi
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // employee punya banyak cuti
    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }
}