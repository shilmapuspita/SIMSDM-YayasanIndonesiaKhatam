@extends('layouts.app')

@section('page-title', 'Tambah Pengajuan Cuti')

@section('content')

{{-- Tombol Kembali --}}
<div class="mb-4">
    <a href="{{ route('cuti.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
        <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-4">

            {{-- Header Card --}}
            <div class="card-header bg-transparent py-3 border-bottom-0">
                <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-calendar-plus me-2"></i>Formulir Cuti Baru</h6>
            </div>

            <div class="card-body pt-0">
                <form action="{{ route('cuti.store') }}" method="POST">
                    @csrf

                    {{-- 1. PILIH PEGAWAI --}}
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">NAMA PEGAWAI <span class="text-danger">*</span></label>
                        <select name="employee_id" class="form-select" required>
                            <option value="" selected disabled>-- Pilih Pegawai --</option>
                            @foreach($employees as $user)
                            <option value="{{ $user->id }}" {{ old('employee_id') == $user->id ? 'selected' : '' }}>
                                @if($user->employee)
                                {{ $user->employee->full_name }} ({{ $user->employee->employee_code }})
                                @else
                                {{ $user->name }} - (Profil Belum Lengkap)
                                @endif
                            </option>
                            @endforeach
                        </select>
                        @error('employee_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- 2. JENIS CUTI --}}
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">JENIS CUTI <span class="text-danger">*</span></label>
                        <select name="leave_type" class="form-select" required>
                            <option value="" selected disabled>-- Pilih Jenis --</option>
                            <option value="annual" {{ old('leave_type') == 'annual' ? 'selected' : '' }}>Cuti Tahunan</option>
                            <option value="sick" {{ old('leave_type') == 'sick' ? 'selected' : '' }}>Sakit</option>
                            <option value="permit" {{ old('leave_type') == 'permit' ? 'selected' : '' }}>Izin / Lainnya</option>
                        </select>
                        @error('leave_type') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    {{-- 3. TANGGAL --}}
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">TANGGAL MULAI <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}" required>
                            @error('start_date') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold text-secondary">TANGGAL SELESAI <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}" required>
                            @error('end_date') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    {{-- 4. ALASAN --}}
                    <div class="mb-4">
                        <label class="form-label small fw-semibold text-secondary">KETERANGAN / ALASAN <span class="text-danger">*</span></label>
                        <textarea name="reason" rows="3" class="form-control" placeholder="Contoh: Acara keluarga, Sakit demam, dll." required>{{ old('reason') }}</textarea>
                        @error('reason') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                    </div>

                    <hr class="border-secondary opacity-10 my-4">

                    {{-- 5. STATUS (Admin Privilege) --}}
                    <div class="bg-light p-3 rounded-3 mb-4 border">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-shield-check text-primary me-2"></i>
                            <label class="form-label small fw-bold text-dark mb-0">STATUS PENGAJUAN</label>
                        </div>

                        <div class="text-muted small mb-2 fst-italic">
                            Sebagai Admin/HRD, Anda dapat langsung menentukan status pengajuan ini.
                        </div>

                        <select name="status" class="form-select border-0 shadow-none bg-white">
                            <option value="approved">✅ Disetujui (Approved)</option>
                            <option value="pending">⏳ Menunggu (Pending)</option>
                            <option value="rejected">❌ Ditolak (Rejected)</option>
                        </select>
                    </div>

                    {{-- TOMBOL AKSI --}}
                    <div class="d-flex gap-2">
                        <button class="btn btn-add rounded-pill">
                            <i class="bi bi-save me-1"></i> Simpan
                        </button>

                        <a href="{{ route('cuti.index') }}"
                            class="btn btn-outline-secondary rounded-pill">
                            Batal
                        </a>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

@endsection