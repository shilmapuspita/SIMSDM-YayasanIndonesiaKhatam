@extends('layouts.app')

@section('page-title', 'Detail Pengajuan Cuti')

@section('content')
<div class="container-fluid p-0">

    {{-- Header & Tombol Kembali --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold mb-0 text-secondary">Detail Pengajuan</h4>
        <a href="{{ route('cuti.index') }}" class="btn btn-outline-secondary rounded-pill btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
        </a>
    </div>

    <div class="row">

        {{-- BAGIAN KIRI: Detail Informasi --}}
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body p-4">
                    <h5 class="card-title fw-semibold mb-4 border-bottom pb-2">Informasi Cuti</h5>

                    <table class="table table-borderless">
                        <tbody>
                            <tr>
                                <td class="text-muted" width="30%">Nama Pegawai</td>
                                <td class="fw-bold fs-5">{{ $leave->user->name ?? 'User Terhapus' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Email</td>
                                <td>{{ $leave->user->email ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <hr class="my-1 dashed">
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Jenis Cuti</td>
                                <td>
                                    @if($leave->leave_type == 'annual')
                                    <span class="badge bg-primary-subtle text-primary border border-primary">Cuti Tahunan</span>
                                    @elseif($leave->leave_type == 'sick')
                                    <span class="badge bg-danger-subtle text-danger border border-danger">Sakit</span>
                                    @else
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary">Izin / Lainnya</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Periode</td>
                                <td>
                                    {{-- Menggunakan casts 'date' di Model, jadi bisa langsung format --}}
                                    {{ $leave->start_date->format('d F Y') }} <span class="mx-2 text-muted">-</span> {{ $leave->end_date->format('d F Y') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Durasi</td>
                                <td class="fw-bold text-dark">
                                    {{ $leave->start_date->diffInDays($leave->end_date) + 1 }} Hari
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted align-top">Keterangan / Alasan</td>
                                <td class="bg-light p-3 rounded border text-secondary fst-italic">
                                    "{{ $leave->reason }}"
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- BAGIAN KANAN: Status & Aksi --}}
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title fw-semibold mb-3">Status Pengajuan</h5>

                    <div class="text-center py-3">
                        @if($leave->status === 'approved')
                        <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                        <h4 class="mt-2 fw-bold text-success">Disetujui</h4>
                        <p class="text-muted small">
                            Disetujui oleh: <strong>{{ $leave->approver->name ?? 'Admin' }}</strong><br>
                            pada {{ $leave->updated_at->format('d M Y H:i') }}
                        </p>

                        @elseif($leave->status === 'rejected')
                        <i class="bi bi-x-circle-fill text-danger" style="font-size: 3rem;"></i>
                        <h4 class="mt-2 fw-bold text-danger">Ditolak</h4>
                        <p class="text-muted small">
                            Ditolak pada {{ $leave->updated_at->format('d M Y') }}
                        </p>

                        @else
                        <i class="bi bi-hourglass-split text-warning" style="font-size: 3rem;"></i>
                        <h4 class="mt-2 fw-bold text-warning">Menunggu</h4>
                        <p class="text-muted small">Menunggu konfirmasi admin/HRD</p>
                        @endif
                    </div>

                    {{-- TOMBOL AKSI CEPAT (Hanya muncul jika status masih Pending) --}}
                    @if($leave->status === 'pending')
                    <hr>
                    <div class="d-grid gap-2">
                        <p class="small text-muted text-center mb-0">Tindakan Cepat:</p>

                        {{-- Tombol Approve --}}
                        <form action="{{ route('cuti.update', $leave->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            {{-- Kirim data hidden agar controller tau ini update status saja --}}
                            <input type="hidden" name="status" value="approved">
                            <input type="hidden" name="employee_id" value="{{ $leave->employee_id }}">
                            <input type="hidden" name="leave_type" value="{{ $leave->leave_type }}">
                            <input type="hidden" name="start_date" value="{{ $leave->start_date->format('Y-m-d') }}">
                            <input type="hidden" name="end_date" value="{{ $leave->end_date->format('Y-m-d') }}">
                            <input type="hidden" name="reason" value="{{ $leave->reason }}">

                            <button type="submit" class="btn btn-success w-100" onclick="return confirm('Setujui pengajuan ini?')">
                                <i class="bi bi-check-lg me-1"></i> Setujui Pengajuan
                            </button>
                        </form>

                        {{-- Tombol Reject --}}
                        <form action="{{ route('cuti.update', $leave->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="rejected">
                            {{-- Data lain harus dikirim ulang agar validasi controller lolos --}}
                            <input type="hidden" name="employee_id" value="{{ $leave->employee_id }}">
                            <input type="hidden" name="leave_type" value="{{ $leave->leave_type }}">
                            <input type="hidden" name="start_date" value="{{ $leave->start_date->format('Y-m-d') }}">
                            <input type="hidden" name="end_date" value="{{ $leave->end_date->format('Y-m-d') }}">
                            <input type="hidden" name="reason" value="{{ $leave->reason }}">

                            <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Tolak pengajuan ini?')">
                                <i class="bi bi-x-lg me-1"></i> Tolak Pengajuan
                            </button>
                        </form>
                    </div>
                    @endif

                </div>
            </div>

            {{-- Tombol Edit & Hapus --}}
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('cuti.edit', $leave->id) }}" class="btn btn-outline-warning w-50 me-2">
                            <i class="bi bi-pencil me-1"></i> Edit
                        </a>

                        <form action="{{ route('cuti.destroy', $leave->id) }}" method="POST" class="w-50">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger w-100" onclick="return confirm('Yakin hapus data ini selamanya?')">
                                <i class="bi bi-trash me-1"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection