@extends('layouts.app')

@section('page-title', 'Detail Absensi')

@section('content')
<div class="card p-4">
    <h5 class="fw-semibold mb-3">Detail Absensi</h5>

    <table class="table table-borderless">
        <tr>
            <th width="200">Nama Pegawai</th>
            <td>: {{ $absensi->employee?->full_name ?? 'Pegawai Terhapus' }}</td>
        </tr>

        <tr>
            <th>Tanggal Absensi</th>
            <td>: {{ \Carbon\Carbon::parse($attendance->attendance_date)->format('d-m-Y') }}</td>
        </tr>

        <tr>
            <th>Jam Masuk</th>
            <td>: {{ $attendance->check_in ?? '-' }}</td>
        </tr>

        <tr>
            <th>Jam Keluar</th>
            <td>: {{ $attendance->check_out ?? '-' }}</td>
        </tr>

        <tr>
            <th>Status Kehadiran</th>
            <td>: {{ ucfirst($attendance->status) }}</td>
        </tr>
    </table>

    <a href="{{ route('karyawan.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>
@endsection