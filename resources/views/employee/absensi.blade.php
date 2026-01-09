@extends('layouts.app')

@section('page-title', 'Absensi Saya')

@section('content')

{{-- STYLE TAMBAHAN --}}
<style>
    .card-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05) !important;
    }

    .digital-clock {
        font-family: 'Courier New', Courier, monospace;
        /* Font ala digital */
        letter-spacing: 2px;
    }

    .gradient-btn {
        background: linear-gradient(45deg, #000080, #0000cd);
        border: none;
        color: white;
    }
</style>

<div class="container-fluid p-0">

    {{-- ALERT PESAN --}}
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill fs-4 me-3"></i>
            <div>
                <strong>Berhasil!</strong> {{ session('success') }}
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm border-0 mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
            <div>
                <strong>Gagal!</strong> {{ session('error') }}
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row g-4">

        {{-- KOLOM KIRI: MAIN ACTION (ABSEN) --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 h-100 position-relative overflow-hidden">
                {{-- Hiasan Background --}}
                <div class="position-absolute top-0 end-0 bg-primary opacity-10 rounded-circle" style="width: 200px; height: 200px; margin-right: -50px; margin-top: -50px;"></div>

                <div class="card-body p-4 p-md-5 text-center">

                    <h5 class="text-muted fw-bold mb-3">WAKTU SAAT INI</h5>

                    {{-- JAM DIGITAL REALTIME --}}
                    <div class="display-3 fw-bold text-dark mb-2 digital-clock" id="realtime-clock">
                        {{ \Carbon\Carbon::now()->format('H:i:s') }}
                    </div>
                    <p class="text-muted mb-4 fs-5">
                        {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </p>

                    <hr class="w-50 mx-auto mb-4 opacity-25">

                    {{-- STATUS BADGE BESAR --}}
                    <div class="mb-4">
                        @if($todayAttendance)
                        @if($todayAttendance->check_out)
                        <div class="d-inline-flex align-items-center px-4 py-2 rounded-pill bg-success bg-opacity-10 text-success border border-success border-opacity-25">
                            <i class="bi bi-house-door-fill me-2"></i> Sudah Pulang
                        </div>
                        @else
                        <div class="d-inline-flex align-items-center px-4 py-2 rounded-pill bg-warning bg-opacity-10 text-warning-emphasis border border-warning border-opacity-25">
                            <i class="bi bi-briefcase-fill me-2"></i> Sedang Bekerja
                        </div>
                        @endif
                        @else
                        <div class="d-inline-flex align-items-center px-4 py-2 rounded-pill bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25">
                            <i class="bi bi-moon-stars-fill me-2"></i> Belum Absen
                        </div>
                        @endif
                    </div>

                    {{-- TOMBOL AKSI UTAMA (BESAR) --}}
                    <div class="d-flex justify-content-center gap-3">

                        {{-- 1. ABSEN MASUK --}}
                        @if(!$todayAttendance)
                        <form action="{{ route('employee.absensi.store') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 py-3 shadow hover-scale">
                                <i class="bi bi-fingerprint me-2 fs-5"></i> <span class="fw-bold">ABSEN MASUK</span>
                            </button>
                        </form>
                        @endif

                        {{-- 2. ABSEN PULANG --}}
                        @if($todayAttendance && !$todayAttendance->check_out)
                        <form action="{{ route('employee.absensi.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="btn btn-danger btn-lg rounded-pill px-5 py-3 shadow hover-scale">
                                <i class="bi bi-box-arrow-right me-2 fs-5"></i> <span class="fw-bold">ABSEN PULANG</span>
                            </button>
                        </form>
                        @endif

                        {{-- 3. SELESAI --}}
                        @if($todayAttendance && $todayAttendance->check_out)
                        <button class="btn btn-light btn-lg rounded-pill px-5 py-3 border text-muted" disabled>
                            <i class="bi bi-check-all me-2"></i> Selesai Hari Ini
                        </button>
                        @endif

                    </div>

                    <div class="mt-4 text-muted small">
                        <i class="bi bi-geo-alt me-1"></i> Lokasi Kantor: Bandung, Indonesia
                    </div>
                </div>
            </div>
        </div>

        {{-- KOLOM KANAN: STATISTIK --}}
        <div class="col-lg-5">
            <div class="row g-3">

                {{-- Card Hadir --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 p-3 card-hover">
                        <div class="d-flex align-items-center">
                            <div class="bg-success bg-opacity-10 p-3 rounded-4 text-success me-3">
                                <i class="bi bi-person-check-fill fs-2"></i>
                            </div>
                            <div>
                                <small class="text-muted fw-bold text-uppercase">Total Kehadiran</small>
                                <h3 class="mb-0 fw-bold text-dark">{{ $stats['hadir'] }} <span class="fs-6 text-muted fw-normal">Hari</span></h3>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card Terlambat --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 p-3 card-hover">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning bg-opacity-10 p-3 rounded-4 text-warning me-3">
                                <i class="bi bi-alarm-fill fs-2"></i>
                            </div>
                            <div>
                                <small class="text-muted fw-bold text-uppercase">Total Terlambat</small>
                                <h3 class="mb-0 fw-bold text-dark">{{ $stats['terlambat'] }} <span class="fs-6 text-muted fw-normal">Kali</span></h3>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card Info Tambahan (Opsional) --}}
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 p-4 bg-primary text-white position-relative overflow-hidden">
                        <i class="bi bi-quote position-absolute text-white opacity-25" style="font-size: 5rem; top: -20px; right: 10px;"></i>
                        <h6 class="fw-bold z-1 position-relative">Disiplin adalah kunci!</h6>
                        <p class="small opacity-75 mb-0 z-1 position-relative">"Kesuksesan dimulai dari hal kecil, termasuk datang tepat waktu."</p>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- TABEL RIWAYAT --}}
    <div class="card border-0 shadow-sm rounded-4 mt-4">
        <div class="card-header bg-white border-0 py-3 rounded-top-4">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="bi bi-clock-history me-2 text-primary"></i>Riwayat Absensi Bulan Ini
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 ps-4 text-secondary text-uppercase small border-0">Tanggal</th>
                        <th class="py-3 text-secondary text-uppercase small border-0">Masuk</th>
                        <th class="py-3 text-secondary text-uppercase small border-0">Pulang</th>
                        <th class="py-3 text-secondary text-uppercase small border-0">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $item)
                    <tr class="border-bottom border-light">
                        <td class="ps-4 py-3">
                            <div class="fw-bold text-dark">{{ \Carbon\Carbon::parse($item->attendance_date)->format('d') }}</div>
                            <small class="text-muted">{{ \Carbon\Carbon::parse($item->attendance_date)->translatedFormat('M Y') }}</small>
                        </td>
                        <td>
                            <span class="fw-semibold text-primary">{{ $item->check_in }}</span>
                        </td>
                        <td>
                            @if($item->check_out)
                            <span class="fw-semibold text-dark">{{ $item->check_out }}</span>
                            @else
                            <span class="text-muted fst-italic">-</span>
                            @endif
                        </td>
                        <td>
                            @if($item->status == 'present')
                            @if(\Carbon\Carbon::parse($item->check_in)->gt('08:00:00'))
                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-3">
                                Terlambat
                            </span>
                            @else
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3">
                                Tepat Waktu
                            </span>
                            @endif
                            @else
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 rounded-pill px-3">
                                {{ ucfirst($item->status) }}
                            </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted">
                            <img src="https://cdn-icons-png.flaticon.com/512/7486/7486744.png" alt="Empty" style="width: 60px; opacity: 0.5;" class="mb-3">
                            <p class="mb-0">Belum ada data absensi bulan ini.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- SCRIPT JAM DIGITAL --}}
<script>
    function updateClock() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('realtime-clock').textContent = `${hours}:${minutes}:${seconds}`;
    }

    // Update setiap 1 detik
    setInterval(updateClock, 1000);
    updateClock(); // Jalankan langsung saat load
</script>

@endsection