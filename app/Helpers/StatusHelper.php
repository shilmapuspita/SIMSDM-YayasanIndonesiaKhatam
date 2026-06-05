<?php

namespace App\Helpers;

class StatusHelper
{
    /**
     * Map employment status to Indonesian label
     */
    public static function employmentStatus($status)
    {
        return match ($status) {
            'contract' => 'Kontrak',
            'permanent' => 'Tetap',
            default => ucfirst($status),
        };
    }

    /**
     * Map attendance status to Indonesian label
     */
    public static function attendanceStatus($status)
    {
        return match ($status) {
            'present' => 'Hadir',
            'late' => 'Terlambat',
            'absent' => 'Tidak Hadir',
            'sick' => 'Sakit',
            'leave' => 'Cuti',
            'permit' => 'Izin',
            'alpha' => 'Alfa',
            'pending' => 'Menunggu',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => ucfirst($status),
        };
    }

    /**
     * Map jenis_pegawai to Indonesian label
     */
    public static function jenisPegawai($jenis)
    {
        return match ($jenis) {
            'management' => 'Management',
            'staff' => 'Staff',
            'guru' => 'Guru',
            'kepsek' => 'Kepala Sekolah',
            'kepala_divisi' => 'Kepala Divisi',
            default => ucfirst(str_replace('_', ' ', $jenis)),
        };
    }

    /**
     * Get working hours for jenis_pegawai (returns array with check_in_time and check_out_time)
     * Format: HH:MM
     * 
     * Guru: 07:00 - 15:00
     * Non-guru (staff, management, kepsek, kepala_divisi): 08:00 - 16:00
     */
    public static function getWorkingHours($jenisPegawai)
    {
        if ($jenisPegawai === 'guru') {
            return [
                'check_in' => '07:00',
                'check_out' => '15:00',
            ];
        }

        // Non-guru: management, staff, kepsek, kepala_divisi
        return [
            'check_in' => '08:00',
            'check_out' => '16:00',
        ];
    }

    /**
     * Get working hours display for jenis_pegawai (returns formatted string)
     * Format: "07:00 - 15:00"
     */
    public static function getWorkingHoursDisplay($jenisPegawai)
    {
        $hours = self::getWorkingHours($jenisPegawai);
        return $hours['check_in'] . ' - ' . $hours['check_out'];
    }
}
