@extends('layouts.app')

@section('page-title', 'Edit Pengajuan Cuti')

@section('content')
<div class="container-fluid p-0">

    {{-- Tombol Kembali --}}
    <div class="mb-3">
        <a href="{{ route('cuti.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <form action="{{ route('cuti.update', $leave->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">

            {{-- KOLOM KIRI: STATUS & PERIODE --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-calendar-check me-2"></i>Status & Periode</h6>
                    </div>
                    <div class="card-body">

                        {{-- STATUS PENGAJUAN --}}
                        <div class="mb-3">
                            <label class="form-label fw-bold">Status Pengajuan</label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="pending" {{ $leave->status == 'pending' ? 'selected' : '' }}>⏳ Menunggu (Pending)</option>
                                <option value="approved" {{ $leave->status == 'approved' ? 'selected' : '' }}>✅ Disetujui (Approved)</option>
                                <option value="rejected" {{ $leave->status == 'rejected' ? 'selected' : '' }}>❌ Ditolak (Rejected)</option>
                            </select>
                            @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <hr>

                        {{-- TANGGAL MULAI --}}
                        <div class="mb-3">
                            <label class="form-label">Tanggal Mulai</label>
                            <input type="date" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                                value="{{ old('start_date', $leave->start_date->format('Y-m-d')) }}" required>
                            @error('start_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- TANGGAL SELESAI --}}
                        <div class="mb-3">
                            <label class="form-label">Tanggal Selesai</label>
                            <input type="date" name="end_date" class="form-control @error('end_date') is-invalid @enderror"
                                value="{{ old('end_date', $leave->end_date->format('Y-m-d')) }}" required>
                            @error('end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: DATA PEGAWAI & KETERANGAN --}}
            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-person-lines-fill me-2"></i>Detail Pengajuan</h6>
                    </div>
                    <div class="card-body p-4">

                        {{-- PILIH PEGAWAI --}}
                        <div class="mb-3">
                            <label class="form-label">Nama Pegawai <span class="text-danger">*</span></label>
                            <select name="employee_id" class="form-select @error('employee_id') is-invalid @enderror">
                                <option value="" disabled>-- Pilih Pegawai --</option>
                                @foreach($employees as $user)
                                <option value="{{ $user->id }}" {{ $leave->employee_id == $user->id ? 'selected' : '' }}>
                                    {{-- Tampilkan nama dari tabel employee, fallback ke user --}}
                                    {{ $user->employee->full_name ?? $user->name }}
                                    {{ !empty($user->employee->employee_code) ? '('.$user->employee->employee_code.')' : '' }}
                                </option>
                                @endforeach
                            </select>
                            @error('employee_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- JENIS CUTI --}}
                        <div class="mb-3">
                            <label class="form-label">Jenis Cuti <span class="text-danger">*</span></label>
                            <select name="leave_type" class="form-select @error('leave_type') is-invalid @enderror">
                                <option value="annual" {{ $leave->leave_type == 'annual' ? 'selected' : '' }}>Cuti Tahunan</option>
                                <option value="sick" {{ $leave->leave_type == 'sick' ? 'selected' : '' }}>Izin Sakit</option>
                                <option value="maternity" {{ $leave->leave_type == 'maternity' ? 'selected' : '' }}>Cuti Melahirkan</option>
                                <option value="permit" {{ $leave->leave_type == 'permit' ? 'selected' : '' }}>Izin Lainnya</option>
                            </select>
                            @error('leave_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- ALASAN --}}
                        <div class="mb-4">
                            <label class="form-label">Keperluan / Alasan <span class="text-danger">*</span></label>
                            <textarea name="reason" class="form-control @error('reason') is-invalid @enderror" rows="4" required>{{ old('reason', $leave->reason) }}</textarea>
                            @error('reason') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- BUTTONS --}}
                        <div class="d-flex gap-2">
                            <button class="btn btn-add rounded-pill">
                                <i class="bi bi-save me-1"></i> Simpan
                            </button>

                            <a href="{{ route('cuti.index') }}"
                                class="btn btn-outline-secondary rounded-pill">
                                Batal
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection