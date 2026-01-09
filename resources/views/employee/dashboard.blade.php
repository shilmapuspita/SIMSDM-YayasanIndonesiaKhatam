@extends('layouts.app')

@section('page-title', 'Dashboard Karyawan')

@section('content')

{{-- SUMMARY CARDS --}}
<div class="row g-4">

    <!-- STATUS ABSENSI HARI INI -->
    <div class="col-md-4">
        <div class="card p-4 h-100">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3">
                    <i class="bi bi-clock-history fs-4"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-semibold">Status Absensi Hari Ini</h6>
                    <small class="text-muted">Kehadiran</small>
                </div>
            </div>
            <span class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill">
                Belum Melakukan Absensi
            </span>
        </div>
    </div>

    <!-- TOTAL HADIR BULAN INI -->
    <div class="col-md-4">
        <div class="card p-4 h-100">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-success bg-opacity-10 text-success rounded-circle p-3 me-3">
                    <i class="bi bi-calendar-check fs-4"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-semibold">Kehadiran Bulan Ini</h6>
                    <small class="text-muted">Total Hari Hadir</small>
                </div>
            </div>
            <h3 class="fw-bold mb-0">12 Hari</h3>
        </div>
    </div>

    <!-- SISA CUTI -->
    <div class="col-md-4">
        <div class="card p-4 h-100">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3 me-3">
                    <i class="bi bi-calendar2-week fs-4"></i>
                </div>
                <div>
                    <h6 class="mb-0 fw-semibold">Sisa Hak Cuti</h6>
                    <small class="text-muted">Tahun Berjalan</small>
                </div>
            </div>
            <h3 class="fw-bold mb-0">8 Hari</h3>
        </div>
    </div>

</div>

<br>

{{-- 3. MENU AKSES CEPAT (Header Rapi) --}}
<h5 class="fw-bold text-dark mb-3"><i class="bi bi-grid-fill me-2 text-secondary"></i>Akses Cepat</h5>

<div class="row g-3">

    {{-- 1. ABSENSI --}}
    <div class="col-6 col-md-4">
        <a href="{{ route('employee.absensi') }}" class="card border-0 shadow-sm text-decoration-none h-100 hover-card">
            <div class="card-body text-center py-4">
                <div class="mb-3 text-success">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="bi bi-fingerprint fs-2"></i>
                    </div>
                </div>
                <h6 class="text-dark fw-bold mb-1">Absen Masuk/Pulang</h6>
                <small class="text-muted" style="font-size: 0.75rem;">Catat kehadiran Anda</small>
            </div>
        </a>
    </div>

    {{-- 2. PENGAJUAN CUTI --}}
    <div class="col-6 col-md-4">
        <a href="{{ route('employee.cuti') }}" class="card border-0 shadow-sm text-decoration-none h-100 hover-card">
            <div class="card-body text-center py-4">
                {{-- Icon Kalender Orange --}}
                <div class="mb-3 text-warning">
                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="bi bi-calendar-plus-fill fs-2"></i>
                    </div>
                </div>
                <h6 class="text-dark fw-bold mb-1">Ajukan Cuti</h6>
                <small class="text-muted" style="font-size: 0.75rem;">Form izin & sakit</small>
            </div>
        </a>
    </div>

    {{-- 3. PROFIL SAYA --}}
    <div class="col-6 col-md-4">
        <a href="{{ route('employee.profile') }}" class="card border-0 shadow-sm text-decoration-none h-100 hover-card">
            <div class="card-body text-center py-4">
                {{-- Icon User Biru --}}
                <div class="mb-3 text-primary">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="bi bi-person-vcard-fill fs-2"></i>
                    </div>
                </div>
                <h6 class="text-dark fw-bold mb-1">Profil Saya</h6>
                <small class="text-muted" style="font-size: 0.75rem;">Lihat biodata diri</small>
            </div>
        </a>
    </div>

</div>

{{-- GRAFIK KEHADIRAN BULAN INI --}}
<div class="card p-4 mt-4">
    <h6 class="fw-semibold mb-3">Kehadiran Bulan Ini</h6>
    <canvas id="attendanceChart" height="100"></canvas>
</div>

{{-- INFORMASI --}}
<div class="alert alert-light border mt-4 rounded-4 d-flex align-items-start gap-2">
    <i class="bi bi-info-circle text-primary fs-5"></i>
    <div>
        <strong>Informasi:</strong>
        <p class="mb-0">
            Pastikan Anda melakukan absensi sesuai jam kerja yang telah ditentukan
            untuk menjaga ketertiban dan keakuratan data kehadiran.
        </p>
    </div>
</div>

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('attendanceChart').getContext('2d');
    const attendanceChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15'], // tanggal contoh
            datasets: [{
                label: 'Hadir',
                data: [1, 1, 0, 1, 1, 1, 0, 1, 1, 1, 1, 1, 0, 1, 1], // contoh data
                backgroundColor: 'rgba(0, 0, 128, 0.7)',
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    stepSize: 1
                }
            }
        }
    });
</script>

@endsection