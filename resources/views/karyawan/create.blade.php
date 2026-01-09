@extends('layouts.app')

@section('page-title', 'Tambah Pegawai Baru')

@section('content')
<div class="container-fluid p-0">

    {{-- Tombol Kembali --}}
    <div class="mb-3">
        <a href="{{ route('karyawan.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <form action="{{ route('karyawan.store') }}" method="POST">
        @csrf
        <div class="row">

            {{-- KOLOM KIRI: AKUN LOGIN --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-person-lock me-2"></i>Akun Login</h6>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info py-2 small">
                            <i class="bi bi-info-circle me-1"></i> Password default adalah <strong>password123</strong>
                        </div>

                        {{-- EMAIL (Penting untuk login) --}}
                        <div class="mb-3">
                            <label class="form-label required">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                value="{{ old('email') }}" placeholder="contoh@email.com" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-briefcase me-2"></i>Status Pekerjaan</h6>
                    </div>
                    <div class="card-body">
                        {{-- KODE PEGAWAI --}}
                        <div class="mb-3">
                            <label class="form-label">Kode Pegawai <span class="text-danger">*</span></label>
                            <input type="text" name="employee_code" class="form-control @error('employee_code') is-invalid @enderror"
                                value="{{ old('employee_code') }}" placeholder="Contoh: EMP-001" required>
                            @error('employee_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- POSISI --}}
                        <div class="mb-3">
                            <label class="form-label">Posisi / Jabatan <span class="text-danger">*</span></label>
                            <input type="text" name="position" class="form-control @error('position') is-invalid @enderror"
                                value="{{ old('position') }}" required>
                            @error('position') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- STATUS --}}
                        <div class="mb-3">
                            <label class="form-label">Status Kepegawaian <span class="text-danger">*</span></label>
                            <select name="employment_status" class="form-select @error('employment_status') is-invalid @enderror" required>
                                <option value="" disabled selected>-- Pilih Status --</option>
                                <option value="permanent" {{ old('employment_status') == 'permanent' ? 'selected' : '' }}>Tetap (Permanent)</option>
                                <option value="contract" {{ old('employment_status') == 'contract' ? 'selected' : '' }}>Kontrak</option>
                            </select>
                            @error('employment_status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- TANGGAL MASUK --}}
                        <div class="mb-3">
                            <label class="form-label">Tanggal Bergabung <span class="text-danger">*</span></label>
                            <input type="date" name="join_date" class="form-control @error('join_date') is-invalid @enderror"
                                value="{{ old('join_date') }}" required>
                            @error('join_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- KOLOM KANAN: BIODATA --}}
            <div class="col-md-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-person-vcard me-2"></i>Biodata Lengkap</h6>
                    </div>
                    <div class="card-body p-4">

                        {{-- NAMA --}}
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control @error('full_name') is-invalid @enderror"
                                value="{{ old('full_name') }}" placeholder="Sesuai KTP" required>
                            @error('full_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                {{-- GENDER --}}
                                <div class="mb-3">
                                    <label class="form-label">Jenis Kelamin <span class="text-danger">*</span></label>
                                    <select name="gender" class="form-select @error('gender') is-invalid @enderror" required>
                                        <option value="" disabled selected>-- Pilih --</option>
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Laki-laki</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Perempuan</option>
                                    </select>
                                    @error('gender') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                {{-- NO HP --}}
                                <div class="mb-3">
                                    <label class="form-label">No Handphone <span class="text-danger">*</span></label>
                                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                        value="{{ old('phone') }}" placeholder="08..." required>
                                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        {{-- TANGGAL LAHIR --}}
                        <div class="mb-3">
                            <label class="form-label">Tanggal Lahir <span class="text-danger">*</span></label>
                            <input type="date" name="birth_date" class="form-control @error('birth_date') is-invalid @enderror"
                                value="{{ old('birth_date') }}" required>
                            @error('birth_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- ALAMAT --}}
                        <div class="mb-4">
                            <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                            <textarea name="address" class="form-control @error('address') is-invalid @enderror" rows="3" required>{{ old('address') }}</textarea>
                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        {{-- BUTTONS --}}
                        <div class="d-flex gap-2">
                            <button class="btn btn-add rounded-pill">
                                <i class="bi bi-save me-1"></i> Simpan
                            </button>

                            <a href="{{ route('karyawan.index') }}"
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