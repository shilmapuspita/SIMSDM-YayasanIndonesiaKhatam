@extends('layouts.app')

@section('page-title', 'Edit Pegawai')

@section('content')
<div class="card p-4">
    <h5 class="fw-semibold mb-3">Edit Data Pegawai</h5>

    <form action="{{ route('karyawan.update', $karyawan->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-3">

            <div class="col-md-6">
                <label class="form-label">Kode Pegawai</label>
                <input type="text" class="form-control"
                    value="{{ $karyawan->employee_code }}" disabled>
            </div>

            <div class="col-md-6">
                <label class="form-label">Nama Lengkap</label>
                <input type="text" name="full_name"
                    class="form-control"
                    value="{{ old('full_name', $karyawan->full_name) }}" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">No. Telepon</label>
                <input type="text" name="phone"
                    class="form-control"
                    value="{{ old('phone', $karyawan->phone) }}"
                    required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Posisi</label>
                <input type="text" name="position"
                    class="form-control"
                    value="{{ old('position', $karyawan->position) }}" required>
            </div>

            <div class="col-md-6">
                <label class="form-label">Status</label>
                <select name="employment_status" class="form-select" required>
                    <option value="permanent" {{ $karyawan->employment_status == 'permanent' ? 'selected' : '' }}>
                        Permanent
                    </option>
                    <option value="contract" {{ $karyawan->employment_status == 'contract' ? 'selected' : '' }}>
                        Contract
                    </option>
                </select>
            </div>

            <div class="col-md-12">
                <label class="form-label">Alamat</label>
                <textarea name="address" class="form-control" rows="3" required>{{ old('address', $karyawan->address) }}</textarea>
            </div>

        </div>

        <div class="d-flex gap-2">
            <button class="btn btn-add rounded-pill">
                <i class="bi bi-save me-1"></i> Simpan
            </button>

            <a href="{{ route('karyawan.index') }}"
                class="btn btn-outline-secondary rounded-pill">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection