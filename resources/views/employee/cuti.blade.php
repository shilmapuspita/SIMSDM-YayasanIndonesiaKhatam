@extends('layouts.app')

@section('page-title', 'Pengajuan Cuti')

@section('content')

<div class="container-fluid p-0">

    {{-- NOTIFIKASI --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="row g-4">

        {{-- 1. FORM PENGAJUAN CUTI --}}
        <div class="col-md-5">
            <div class="card border-0 shadow-sm p-4 h-100">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3">
                        <i class="bi bi-calendar-plus fs-4"></i>
                    </div>
                    <h5 class="fw-bold mb-0" style="color: #000080;">Ajukan Cuti Baru</h5>
                </div>

                <form action="{{ route('employee.cuti.store') }}" method="POST">
                    @csrf

                    {{-- [PERBAIKAN UTAMA] Tambahkan Input Leave Type --}}
                    <div class="mb-3">
                        <label class="form-label text-secondary small">Jenis Cuti / Izin</label>
                        <select name="leave_type" class="form-select" required>
                            <option value="" disabled selected>Pilih Jenis...</option>
                            <option value="annual">Cuti Tahunan</option>
                            <option value="sick">Sakit</option>
                            <option value="permit">Izin Keperluan</option>
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label text-secondary small">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="start_date" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-secondary small">Tanggal Selesai</label>
                            <input type="date" class="form-control" name="end_date" required>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label text-secondary small">Alasan Cuti</label>
                        <textarea class="form-control" name="reason" rows="3" placeholder="Contoh: Sakit Demam / Acara Nikahan" required></textarea>
                    </div>

                    <button type="submit" class="btn-add w-100 rounded-pill">
                        <i class="bi bi-send me-1"></i> Kirim Pengajuan
                    </button>
                </form>
            </div>
        </div>

        {{-- 2. RIWAYAT CUTI --}}
        <div class="col-md-7">
            <div class="card border-0 shadow-sm p-4 h-100">
                <h5 class="fw-bold mb-3" style="color: #000080;">Riwayat Cuti</h5>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Jenis & Periode</th>
                                <th>Status</th>
                                <th>Ket</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($leaves as $index => $leave)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>
                                    {{-- Tampilkan Jenis Cuti --}}
                                    <span class="fw-bold text-dark d-block">
                                        @if($leave->leave_type == 'annual') Cuti Tahunan
                                        @elseif($leave->leave_type == 'sick') Sakit
                                        @else Izin @endif
                                    </span>
                                    {{-- Tampilkan Tanggal --}}
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($leave->start_date)->format('d M') }} -
                                        {{ \Carbon\Carbon::parse($leave->end_date)->format('d M Y') }}
                                    </small>
                                </td>
                                <td>
                                    @if($leave->status === 'approved')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Disetujui</span>
                                    @elseif($leave->status === 'pending')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">Menunggu</span>
                                    @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">Ditolak</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-light rounded-circle" data-bs-toggle="tooltip" title="{{ $leave->reason }}">
                                        <i class="bi bi-info-circle"></i>
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">Belum ada pengajuan cuti</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>

@endsection

@push('scripts')
<script>
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    })
</script>
@endpush