<?php

namespace App\Helpers;

class UIHelper
{
    /**
     * Get role label in Indonesian
     */
    public static function getRoleLabel($role)
    {
        return match ($role) {
            'admin' => 'Super Admin',
            'hrd' => 'HRD',
            'employee' => 'Pegawai',
            default => ucfirst($role),
        };
    }

    /**
     * Get role badge CSS class
     */
    public static function getRoleBadgeClass($role)
    {
        return match ($role) {
            'admin' => 'bg-primary text-white',
            'hrd' => 'bg-purple text-white',
            'employee' => 'bg-success text-white',
            default => 'bg-secondary text-white',
        };
    }

    /**
     * Get role badge HTML
     */
    public static function getRoleBadge($role)
    {
        $class = self::getRoleBadgeClass($role);
        $label = self::getRoleLabel($role);
        return '<span class="badge ' . $class . ' fw-bold">' . $label . '</span>';
    }

    /**
     * Get status label in Indonesian
     */
    public static function getStatusLabel($status)
    {
        return match ($status) {
            'active' => 'Aktif',
            'inactive' => 'Nonaktif',
            'online' => 'Online',
            'offline' => 'Offline',
            'present' => 'Hadir',
            'late' => 'Terlambat',
            'absent' => 'Alpa',
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => ucfirst($status),
        };
    }

    /**
     * Get status badge CSS class
     */
    public static function getStatusBadgeClass($status)
    {
        return match ($status) {
            'active', 'online', 'present', 'approved' => 'bg-success text-white',
            'inactive', 'offline', 'absent', 'rejected' => 'bg-danger text-white',
            'late', 'pending' => 'bg-warning text-dark',
            default => 'bg-secondary text-white',
        };
    }

    /**
     * Get status badge HTML
     */
    public static function getStatusBadge($status)
    {
        $class = self::getStatusBadgeClass($status);
        $label = self::getStatusLabel($status);
        return '<span class="badge ' . $class . ' fw-bold">' . $label . '</span>';
    }

    /**
     * Get activity action label in Indonesian
     */
    public static function getActivityLabel($activity)
    {
        return match ($activity) {
            'login' => 'Masuk Sistem',
            'logout' => 'Keluar Sistem',
            'create' => 'Membuat Data',
            'update' => 'Perbarui Data',
            'delete' => 'Hapus Data',
            'check_in' => 'Absen Masuk',
            'check_out' => 'Absen Pulang',
            'approve' => 'Setujui',
            'reject' => 'Tolak',
            'reset_password' => 'Atur Ulang Sandi',
            default => ucfirst(str_replace('_', ' ', $activity)),
        };
    }

    /**
     * Get button classes for actions
     */
    public static function getActionButtonClass($action)
    {
        return match ($action) {
            'create', 'add' => 'btn-success',
            'edit', 'reset-password' => 'btn-warning',
            'delete' => 'btn-danger',
            'view', 'show' => 'btn-info',
            'filter', 'search' => 'btn-primary',
            'reset' => 'btn-secondary',
            'approve' => 'btn-success',
            'reject' => 'btn-danger',
            default => 'btn-primary',
        };
    }
}
