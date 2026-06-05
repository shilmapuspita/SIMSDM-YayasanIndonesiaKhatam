<?php

namespace App\Exports;

use App\Models\Attendance;
use App\Helpers\StatusHelper;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping
{
    protected array $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Attendance::with('employee')->orderBy('attendance_date', 'desc');

        if (!empty($this->filters['month'])) {
            $query->whereMonth('attendance_date', $this->filters['month']);
        }

        if (!empty($this->filters['year'])) {
            $query->whereYear('attendance_date', $this->filters['year']);
        }

        if (!empty($this->filters['employee_id'])) {
            $query->where('employee_id', $this->filters['employee_id']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Nama Pegawai',
            'Jenis Pegawai',
            'Tanggal',
            'Jam Masuk',
            'Jam Keluar',
            'Status',
        ];
    }

    public function map($attendance): array
    {
        return [
            $attendance->employee?->full_name ?? '-',
            StatusHelper::jenisPegawai($attendance->employee?->jenis_pegawai ?? 'staff'),
            $attendance->attendance_date?->format('d-m-Y') ?? '-',
            $attendance->check_in ?? '-',
            $attendance->check_out ?? '-',
            StatusHelper::attendanceStatus($attendance->status),
        ];
    }
}
