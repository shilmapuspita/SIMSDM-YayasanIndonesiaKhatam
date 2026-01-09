@extends('layouts.app')

@section('page-title', 'Dashboard Administrator')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- 2. STATISTIK UTAMA --}}
    <div class="row g-4 mb-4">

        {{-- Card: Total Users --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">Total Pengguna</h6>
                        <h3 class="mb-0 fw-bold text-dark">{{ $totalUsers ?? '0' }}</h3>
                    </div>
                    <div class="icon-shape bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                        <i class="bi bi-people-fill fs-3"></i>
                    </div>
                </div>
                <div class="position-absolute bottom-0 start-0 w-100 bg-primary" style="height: 4px;"></div>
            </div>
        </div>

        {{-- Card: Total Employee --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">Staff Aktif</h6>
                        <h3 class="mb-0 fw-bold text-dark">{{ $totalEmployees ?? '0' }}</h3>
                    </div>
                    <div class="icon-shape bg-success bg-opacity-10 text-success rounded-3 p-3">
                        <i class="bi bi-person-badge-fill fs-3"></i>
                    </div>
                </div>
                <div class="position-absolute bottom-0 start-0 w-100 bg-success" style="height: 4px;"></div>
            </div>
        </div>

        {{-- Card: Tim HRD --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">Admin & HRD</h6>
                        <h3 class="mb-0 fw-bold text-dark">{{ $totalHRD ?? '0' }}</h3>
                    </div>
                    <div class="icon-shape bg-info bg-opacity-10 text-info rounded-3 p-3">
                        <i class="bi bi-shield-lock-fill fs-3"></i>
                    </div>
                </div>
                <div class="position-absolute bottom-0 start-0 w-100 bg-info" style="height: 4px;"></div>
            </div>
        </div>

        {{-- Card: User Online --}}
        <div class="col-12 col-md-6 col-xl-3">
            <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden">
                <div class="card-body p-4 d-flex align-items-center justify-content-between">
                    <div>
                        <h6 class="text-muted text-uppercase fw-bold mb-1" style="font-size: 0.75rem;">Sesi Aktif</h6>
                        <h3 class="mb-0 fw-bold text-dark">{{ $activeUsers ?? '0' }}</h3>
                    </div>
                    <div class="icon-shape bg-warning bg-opacity-10 text-warning rounded-3 p-3">
                        <i class="bi bi-activity fs-3"></i>
                    </div>
                </div>
                <div class="position-absolute bottom-0 start-0 w-100 bg-warning" style="height: 4px;"></div>
            </div>
        </div>
    </div>

    {{-- 3. QUICK ACTIONS (Gaya Dashboard HRD - Grid Besar) --}}
    <h5 class="fw-bold text-dark mb-3"><i class="bi bi-lightning-charge-fill me-2 text-warning"></i>Kelola Sistem</h5>
    <div class="row g-3 mb-4">

        <div class="col-6 col-md-3">
            {{-- Ganti href="#" dengan route user create kamu --}}
            <a href="#" class="card border-0 shadow-sm text-decoration-none h-100 hover-card">
                <div class="card-body text-center py-4">
                    <div class="mb-3 text-primary bg-primary bg-opacity-10 rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="bi bi-person-plus-fill fs-3"></i>
                    </div>
                    <h6 class="text-dark fw-bold mb-1">Tambah User</h6>
                    <small class="text-muted">Buat akun baru</small>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-3">
            <a href="#" class="card border-0 shadow-sm text-decoration-none h-100 hover-card">
                <div class="card-body text-center py-4">
                    <div class="mb-3 text-purple bg-purple bg-opacity-10 rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="bi bi-person-gear fs-3"></i>
                    </div>
                    <h6 class="text-dark fw-bold mb-1">Role & Akses</h6>
                    <small class="text-muted">Atur hak akses</small>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-3">
            <a href="#" class="card border-0 shadow-sm text-decoration-none h-100 hover-card">
                <div class="card-body text-center py-4">
                    <div class="mb-3 text-secondary bg-secondary bg-opacity-10 rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="bi bi-sliders fs-3"></i>
                    </div>
                    <h6 class="text-dark fw-bold mb-1">Pengaturan</h6>
                    <small class="text-muted">Konfigurasi sistem</small>
                </div>
            </a>
        </div>

        <div class="col-6 col-md-3">
            <a href="#" class="card border-0 shadow-sm text-decoration-none h-100 hover-card">
                <div class="card-body text-center py-4">
                    <div class="mb-3 text-danger bg-danger bg-opacity-10 rounded-circle mx-auto d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="bi bi-journal-text fs-3"></i>
                    </div>
                    <h6 class="text-dark fw-bold mb-1">Log Aktivitas</h6>
                    <small class="text-muted">Audit trail user</small>
                </div>
            </a>
        </div>
    </div>

    {{-- 4. CHART & LIST USER BARU --}}
    <div class="row g-4">

        {{-- Kiri: Chart Statistik --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-graph-up-arrow me-2 text-primary"></i>Statistik Pendaftaran User</h6>
                    <select class="form-select form-select-sm w-auto border-0 bg-light fw-bold text-secondary">
                        <option>Tahun Ini</option>
                        <option>Bulan Ini</option>
                    </select>
                </div>
                <div class="card-body">
                    <canvas id="userGrowthChart" style="max-height: 300px;"></canvas>
                </div>
            </div>
        </div>

        {{-- Kanan: User Terbaru --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-0">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-success"></i>User Baru Ditambahkan</h6>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse($latestUsers ?? [] as $user)
                        <li class="list-group-item border-0 d-flex align-items-center px-4 py-3 hover-bg-light">
                            <div class="avatar-initials bg-light text-primary rounded-circle me-3 d-flex align-items-center justify-content-center fw-bold border" style="width: 40px; height: 40px;">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 text-dark fw-bold fs-6">{{ $user->name }}</h6>
                                <small class="text-muted" style="font-size: 0.75rem;">{{ $user->email }}</small>
                            </div>
                            <div>
                                <span class="badge {{ $user->role == 'admin' ? 'bg-danger' : ($user->role == 'hrd' ? 'bg-info' : 'bg-success') }} bg-opacity-75 rounded-pill" style="font-size: 0.65rem;">
                                    {{ strtoupper($user->role) }}
                                </span>
                            </div>
                        </li>
                        @empty
                        <li class="list-group-item border-0 text-center py-4 text-muted">
                            Belum ada user baru.
                        </li>
                        @endforelse
                    </ul>
                </div>
                <div class="card-footer bg-white border-0 text-center py-3">
                    <a href="#" class="text-decoration-none small fw-bold text-primary">Lihat Semua User <i class="bi bi-arrow-right ms-1"></i></a>
                </div>
            </div>
        </div>
    </div>

    {{-- 5. INFO SERVER (Footer Alert) --}}
    <div class="alert alert-light border mt-4 rounded-3 d-flex align-items-center shadow-sm">
        <i class="bi bi-info-circle-fill text-primary fs-4 me-3"></i>
        <div>
            <strong class="text-dark">Info Sistem:</strong>
            <span class="text-muted">Backup database otomatis terakhir dilakukan pada <span class="fw-bold text-dark">{{ now()->subDay()->format('d M Y, 23:00') }}</span>. Sistem berjalan normal.</span>
        </div>
    </div>

</div>

{{-- CSS KHUSUS --}}
<style>
    /* 1. HOVER EFFECT YANG LEBIH SMOOTH */
    .hover-card {
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        /* Animasi gerak lebih luwes */
        border: 1px solid transparent;
        /* Persiapan border */
    }

    .hover-card:hover {
        transform: translateY(-5px);
        /* Naik ke atas dikit */
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1) !important;
        /* Bayangan lebih soft & lebar */
        background-color: #ffffff;
        border-color: #dee2e6 !important;
        /* Tambah border tipis pas hover biar tegas */
        z-index: 10;
    }

    /* 2. WARNA UNGU CUSTOM (FIXED) */
    .text-purple {
        color: #6f42c1 !important;
        /* Pakai !important biar ga ketimpa */
    }

    .bg-purple {
        background-color: #6f42c1 !important;
    }

    /* INI KUNCINYA: Biar bg-opacity-10 jalan di warna ungu custom */
    /* Kita paksa warnanya jadi RGBA transparan (0.1 opacity) */
    .bg-purple.bg-opacity-10 {
        background-color: rgba(111, 66, 193, 0.1) !important;
    }

    /* 3. TAMBAHAN: Hover List User */
    .hover-bg-light {
        transition: background-color 0.2s ease;
        border-radius: 8px;
        /* Biar pas di-hover sudutnya tumpul */
    }

    .hover-bg-light:hover {
        background-color: #f1f3f5 !important;
        /* Abu sangat muda */
        cursor: pointer;
    }
</style>

{{-- SCRIPT CHART --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('userGrowthChart').getContext('2d');

    // Gradient Warna Chart biar Mewah
    let gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(13, 110, 253, 0.3)'); // Biru Transparan Atas
    gradient.addColorStop(1, 'rgba(13, 110, 253, 0.0)'); // Putih Bawah

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'User Baru',
                data: [5, 12, 15, 10, 22, 18, 25, 30, 28, 35, 40, 45], // Data Dummy Cantik
                borderColor: '#0d6efd',
                backgroundColor: gradient,
                borderWidth: 2,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#0d6efd',
                pointRadius: 4,
                pointHoverRadius: 6,
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#343a40',
                    padding: 10,
                    cornerRadius: 8,
                    displayColors: false,
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        borderDash: [2, 4],
                        color: '#e9ecef'
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
                    }
                }
            }
        }
    });
</script>

@endsection