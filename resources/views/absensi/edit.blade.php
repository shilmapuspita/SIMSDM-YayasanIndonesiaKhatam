@extends('layouts.app')

@section('page-title', 'Edit Absensi')

@section('content')
<div class="card p-4">

    <h5 class="fw-semibold mb-4">Form Edit Absensi</h5>

    @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form action="{{ route('absensi.update', $attendance->id) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- Pegawai --}}
        <div class="mb-3">
            <label class="form-label">Pegawai</label>
            <select name="employee_id" class="form-select" required>
                @foreach ($employees as $employee)
                <option value="{{ $employee->id }}"
                    {{ $attendance->employee_id == $employee->id ? 'selected' : '' }}>
                    {{ $employee->full_name }}
                </option>
                @endforeach
            </select>
        </div>

        {{-- Tanggal --}}
        <div class="mb-3">
            <label class="form-label">Tanggal</label>
            <input type="date"
                name="attendance_date"
                class="form-control"
                value="{{ old('attendance_date', $attendance->attendance_date->format('Y-m-d')) }}"
                required>
        </div>

        <div class="row">
            {{-- Jam Masuk --}}
            <div class="col-md-6 mb-3">
                <label class="form-label">Jam Masuk</label>
                <input type="time"
                    name="check_in"
                    class="form-control"
                    value="{{ old('check_in', $attendance->check_in) }}">
            </div>

            {{-- Jam Keluar --}}
            <div class="col-md-6 mb-3">
                <label class="form-label">Jam Keluar</label>
                <input type="time"
                    name="check_out"
                    class="form-control"
                    value="{{ old('check_out', $attendance->check_out) }}">
            </div>
        </div>

        {{-- Status --}}
        <div class="mb-3">
            <label class="form-label">Status Kehadiran</label>
            <select name="status" class="form-select" required>
                <option value="present" {{ $attendance->status == 'present' ? 'selected' : '' }}>Hadir</option>
                <option value="sick" {{ $attendance->status == 'sick' ? 'selected' : '' }}>Sakit</option>
                <option value="leave" {{ $attendance->status == 'leave' ? 'selected' : '' }}>Izin</option>
                <option value="absent" {{ $attendance->status == 'absent' ? 'selected' : '' }}>Alpha</option>
            </select>
        </div>

        {{-- Button --}}
        <div class="d-flex gap-2">
            <button class="btn btn-add rounded-pill">
                <i class="bi bi-save me-1"></i> Update
            </button>

            <a href="{{ route('absensi.index') }}"
                class="btn btn-outline-secondary rounded-pill">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection