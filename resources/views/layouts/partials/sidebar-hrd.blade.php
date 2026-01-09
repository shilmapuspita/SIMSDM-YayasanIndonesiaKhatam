<aside class="sidebar d-flex flex-column text-white" style="width: 260px; min-height: 100vh; background-color: #000080;">

    {{-- STYLE KHUSUS --}}
    <style>
        .menu-aktif {
            background-color: #f77042 !important;
            color: #ffffff !important;
            font-weight: bold !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            border-radius: 5px;
        }

        .nav-link {
            color: #ffffff;
            transition: all 0.3s;
        }

        .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
        }
    </style>

    {{-- BRAND / HEADER --}}
    <div class="p-4 border-bottom border-light border-opacity-25 text-center">
        <div class="bg-white p-2 rounded-circle d-inline-block shadow-sm">
            <img src="{{ asset('assets/img/logo.png') }}"
                alt="Logo"
                style="width: 50px; height: 50px; object-fit: contain;">
        </div>
        <h6 class="mt-3 mb-0 fw-bold" style="color: #ffffff;">Yayasan Indonesia Khatam</h6>
    </div>

    {{-- MENU NAVIGASI --}}
    <nav class="flex-grow-1 p-3">
        <ul class="nav flex-column gap-2">

            {{-- 1. DASHBOARD --}}
            <li class="nav-item">
                <a href="{{ route('hrd.dashboard') }}"
                    class="nav-link d-flex align-items-center {{ request()->routeIs('hrd.dashboard') ? 'menu-aktif' : '' }}">
                    <i class="bi bi-speedometer2 me-3"></i> Dashboard
                </a>
            </li>

            {{-- GROUP: MANAJEMEN SDM --}}
            <li class="mt-3 px-3 text-uppercase small fw-bold" style="color: rgba(255, 255, 255, 0.5); font-size: 0.75rem;">
                Manajemen SDM
            </li>

            {{-- 2. DATA PEGAWAI (Route: karyawan.index) --}}
            <li class="nav-item">
                <a href="{{ route('karyawan.index') }}"
                    class="nav-link d-flex align-items-center {{ request()->routeIs('karyawan*') ? 'menu-aktif' : '' }}">
                    <i class="bi bi-people-fill me-3"></i> Data Pegawai
                </a>
            </li>

            {{-- 3. PERSETUJUAN CUTI (Route: cuti.index) --}}
            <li class="nav-item">
                <a href="{{ route('cuti.index') }}"
                    class="nav-link d-flex align-items-center {{ request()->routeIs('cuti*') ? 'menu-aktif' : '' }}">
                    <i class="bi bi-check-circle-fill me-3"></i> Persetujuan Cuti
                </a>
            </li>

            {{-- 4. REKAP ABSENSI (Route: absensi.index) --}}
            <li class="nav-item">
                <a href="{{ route('absensi.index') }}"
                    class="nav-link d-flex align-items-center {{ request()->routeIs('absensi*') ? 'menu-aktif' : '' }}">
                    <i class="bi bi-calendar-week me-3"></i> Rekap Absensi
                </a>
            </li>

            {{-- GROUP: PENGATURAN --}}
            <!-- <li class="mt-3 px-3 text-uppercase small fw-bold" style="color: rgba(255, 255, 255, 0.5); font-size: 0.75rem;">
                Pengaturan
            </li>

            {{-- 5. PROFIL SAYA --}}
            <li class="nav-item">
                <a href="{{ route('profile.edit') }}"
                    class="nav-link d-flex align-items-center {{ request()->routeIs('profile*') ? 'menu-aktif' : '' }}">
                    <i class="bi bi-person-gear me-3"></i> Profil Saya
                </a>
            </li> -->

        </ul>
    </nav>

    {{-- LOGOUT --}}
    <div class="p-3 border-top border-light border-opacity-25">
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button class="btn btn-outline-light w-100 rounded-pill">
                <i class="bi bi-box-arrow-right me-1"></i> Logout
            </button>
        </form>
    </div>

</aside>