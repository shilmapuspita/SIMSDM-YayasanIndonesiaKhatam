@extends('layouts.app')

@section('page-title', 'Tambah Absensi')

@section('content')
<div class="card p-4">

    <h5 class="fw-semibold mb-4">Form Tambah Absensi</h5>

    <form action="{{ route('absensi.store') }}" method="POST">
        @csrf

        {{-- Pegawai --}}
        <div class="mb-3">
            <label class="form-label">Pegawai</label>
            <select name="employee_id" class="form-select" required>
                <option value="">-- Pilih Pegawai --</option>
                @foreach ($employees as $employee)
                <option value="{{ $employee->id }}">
                    {{ $employee->full_name }}
                </option>
                @endforeach
            </select>
        </div>

        {{-- Tanggal --}}
        <div class="mb-3">
            <label class="form-label">Tanggal</label>
            <input type="date" name="attendance_date" class="form-control" required>
        </div>

        <div class="row">
            {{-- Jam Masuk --}}
            <div class="col-md-6 mb-3">
                <label class="form-label">Jam Masuk</label>
                <input type="time" name="check_in" class="form-control">
            </div>

            {{-- Jam Keluar --}}
            <div class="col-md-6 mb-3">
                <label class="form-label">Jam Keluar</label>
                <input type="time" name="check_out" class="form-control">
            </div>
        </div>

        {{-- Status --}}
        <div class="mb-3">
            <label class="form-label">Status Kehadiran</label>
            <select name="status" class="form-select" required>
                <option value="present">Hadir</option>
                <option value="leave">Izin</option>
                <option value="sick">Sakit</option>
                <option value="absent">Alpha</option>
            </select>
        </div>

        {{-- BUTTON --}}
        <div class="d-flex gap-2">
            <button class="btn btn-add rounded-pill">
                <i class="bi bi-save me-1"></i> Simpan
            </button>

            <a href="{{ route('absensi.index') }}"
                class="btn btn-outline-secondary rounded-pill">
                Batal
            </a>
        </div>

    </form>
</div>
@endsection