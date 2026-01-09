@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- 2. KARTU STATISTIK (Data dari Controller) --}}
    <div class="row g-4 mb-4">

        {{-- Card: Total Employees --}}
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.8rem;">Total Pegawai</h6>
                        <h1 class="mb-0 fw-bold text-dark display-5">{{ $totalEmployees }}</h1>
                        <a href="{{ route('karyawan.index') }}" class="small text-primary text-decoration-none mt-2 d-inline-block fw-bold">
                            Lihat Semua Data <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    <div class="icon-shape bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                        <i class="bi bi-people-fill fs-2"></i>
                    </div>
                </div>
                {{-- Hiasan Garis --}}
                <div class="position-absolute bottom-0 start-0 w-100 bg-primary" style="height: 4px;"></div>
            </div>
        </div>

        {{-- Card: Pending Leaves (Ada Logika Warna) --}}
        <div class="col-12 col-md-6">
            <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.8rem;">Permintaan Cuti (Pending)</h6>

                        {{-- Kalau ada cuti pending, angkanya Merah. Kalau 0, Hijau --}}
                        <h1 class="mb-0 fw-bold display-5 {{ $pendingLeaves > 0 ? 'text-danger' : 'text-success' }}">
                            {{ $pendingLeaves }}
                        </h1>

                        <a href="{{ route('cuti.index') }}" class="small {{ $pendingLeaves > 0 ? 'text-danger' : 'text-success' }} text-decoration-none mt-2 d-inline-block fw-bold">
                            {{ $pendingLeaves > 0 ? 'Segera Proses Approval' : 'Semua Aman' }} <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                    <div class="icon-shape {{ $pendingLeaves > 0 ? 'bg-danger text-danger' : 'bg-success text-success' }} bg-opacity-10 rounded-3 p-3">
                        <i class="bi {{ $pendingLeaves > 0 ? 'bi-exclamation-circle-fill' : 'bi-check-circle-fill' }} fs-2"></i>
                    </div>
                </div>
                {{-- Hiasan Garis --}}
                <div class="position-absolute bottom-0 start-0 w-100 {{ $pendingLeaves > 0 ? 'bg-danger' : 'bg-success' }}" style="height: 4px;"></div>
            </div>
        </div>

    </div>

    {{-- 3. MENU AKSES CEPAT (Static Links) --}}
    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-grid-fill me-2 text-secondary"></i>Akses Cepat</h5>
    <div class="row g-3">

        <div class="col-6 col-md-3">
            <a href="{{ route('karyawan.create') }}" class="card border-0 shadow-sm text-decoration-none h-100 hover-card">
                <div class="card-body text-center py-4">
                    <div class="mb-2 text-primary"><i class="bi bi-person-plus-fill fs-3"></i></div>
                    <h6 class="text-dark fw-bold mb-0">Tambah Pegawai</h6>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-3">
            <a href="{{ route('absensi.create') }}" class="card border-0 shadow-sm text-decoration-none h-100 hover-card">
                <div class="card-body text-center py-4">
                    <div class="mb-2 text-success"><i class="bi bi-qr-code-scan fs-3"></i></div>
                    <h6 class="text-dark fw-bold mb-0">Input Absensi</h6>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-3">
            <a href="{{ route('absensi.index') }}" class="card border-0 shadow-sm text-decoration-none h-100 hover-card">
                <div class="card-body text-center py-4">
                    <div class="mb-2 text-info"><i class="bi bi-table fs-3"></i></div>
                    <h6 class="text-dark fw-bold mb-0">Rekap Absensi</h6>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-3">
            <a href="{{ route('cuti.index') }}" class="card border-0 shadow-sm text-decoration-none h-100 hover-card">
                <div class="card-body text-center py-4">
                    <div class="mb-2 text-warning"><i class="bi bi-calendar-check-fill fs-3"></i></div>
                    <h6 class="text-dark fw-bold mb-0">Approval Cuti</h6>
                </div>
            </a>
        </div>

    </div>

</div>

{{-- Style Tambahan Dikit --}}
<style>
    .hover-card {
        transition: all 0.3s ease;
    }

    .hover-card:hover {
        transform: translateY(-5px);
    }
</style>
@endsection