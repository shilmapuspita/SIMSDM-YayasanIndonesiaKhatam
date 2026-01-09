<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'is_active'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /* ================= RELATIONSHIP ================= */

    // 1 user punya 1 data employee
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    // user (admin/hrd) menyetujui banyak cuti
    public function approvedLeaves()
    {
        return $this->hasMany(Leave::class, 'approved_by');
    }
}