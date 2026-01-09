@extends('layouts.app')

@section('page-title', 'Detail Pegawai')

@section('content')
<div class="card p-4">
    <h5 class="fw-semibold mb-3">Detail Pegawai</h5>

    <table class="table table-borderless">
        <tr>
            <th width="200">Kode Pegawai</th>
            <td>: {{ $karyawan->employee_code }}</td>
        </tr>
        <tr>
            <th>Nama Lengkap</th>
            <td>: {{ $karyawan->full_name }}</td>
        </tr>
        <tr>
            <th>Posisi</th>
            <td>: {{ $karyawan->position }}</td>
        </tr>
        <tr>
            <th>Status</th>
            <td>: {{ ucfirst($karyawan->employment_status) }}</td>
        </tr>
        <tr>
            <th>Tanggal Bergabung</th>
            <td>: {{ $karyawan->join_date ?? '-' }}</td>
        </tr>
    </table>

    <a href="{{ route('karyawan.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>
@endsection