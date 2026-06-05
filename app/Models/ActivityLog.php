<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';

    protected $fillable = [
        'user_id',
        'activity',
        'description',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getActionBadgeClass()
    {
        return match ($this->activity) {
            'login' => 'bg-success',
            'logout' => 'bg-secondary',
            'create_employee' => 'bg-primary',
            'update_employee' => 'bg-info',
            'delete_employee' => 'bg-danger',
            'check_in' => 'bg-success',
            'check_out' => 'bg-warning',
            'create_user' => 'bg-primary',
            'update_user' => 'bg-info',
            'delete_user' => 'bg-danger',
            'leave_request' => 'bg-primary',
            'approve_leave' => 'bg-success',
            'reject_leave' => 'bg-danger',
            'delete_leave' => 'bg-danger',
            'update_leave' => 'bg-info',
            default => 'bg-light text-dark',
        };
    }

    public function getActionName()
    {
        return $this->getActivityName();
    }

    public function getActivityName()
    {
        return match ($this->activity) {
            'login' => 'Login',
            'logout' => 'Logout',
            'create_employee' => 'Tambah Pegawai',
            'update_employee' => 'Update Pegawai',
            'delete_employee' => 'Hapus Pegawai',
            'check_in' => 'Absensi Masuk',
            'check_out' => 'Absensi Pulang',
            'create_user' => 'Tambah User',
            'update_user' => 'Update User',
            'delete_user' => 'Hapus User',
            'leave_request' => 'Pengajuan Cuti',
            'approve_leave' => 'Setujui Cuti',
            'reject_leave' => 'Tolak Cuti',
            'delete_leave' => 'Hapus Pengajuan Cuti',
            'update_leave' => 'Edit Pengajuan Cuti',
            default => ucfirst(str_replace('_', ' ', $this->activity)),
        };
    }
}
