<aside class="sidebar d-flex flex-column text-white flex-shrink-0" style="width: 260px; min-height: 100vh; background-color: #000080;">

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

        /* .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1);
            color: #ffffff;
            transform: translateX(5px);
            Efek geser dikit biar kece
        } */
    </style>

    {{-- BRAND / HEADER --}}
    <div class="p-4 border-bottom border-light border-opacity-25 text-center">
        <div class="bg-white p-2 rounded-circle d-inline-block shadow-sm">
            <img src="{{ asset('assets/img/logo.png') }}"
                alt="Logo"
                style="width: 50px; height: 50px; object-fit: contain;">
        </div>

        <h6 class="text-white-55 d-block">Yayasan Indonesia Khatam</h6>
    </div>

    {{-- MENU NAVIGASI --}}
    <nav class="flex-grow-1 p-3">
        <ul class="nav flex-column gap-2">

            {{-- Dashboard --}}
            <li class="nav-item">
                <a href="{{ route('employee.dashboard') }}"
                    class="nav-link d-flex align-items-center {{ request()->routeIs('employee.dashboard') ? 'menu-aktif' : '' }}">
                    <i class="bi bi-speedometer2 me-3"></i> Dashboard
                </a>
            </li>

            {{-- GROUP: AKTIVITAS --}}
            <li class="mt-3 px-3 text-uppercase small fw-bold" style="color: rgba(255, 255, 255, 0.5); font-size: 0.75rem;">
                Aktivitas Utama
            </li>

            {{-- Absensi --}}
            <li class="nav-item">
                <a href="{{ route('employee.absensi') }}"
                    class="nav-link d-flex align-items-center {{ request()->routeIs('employee.absensi') ? 'menu-aktif' : '' }}">
                    <i class="bi bi-calendar-check me-3"></i> Absensi
                </a>
            </li>

            {{-- Cuti --}}
            <li class="nav-item">
                <a href="{{ route('employee.cuti') }}"
                    class="nav-link d-flex align-items-center {{ request()->routeIs('employee.cuti') ? 'menu-aktif' : '' }}">
                    <i class="bi bi-journal-text me-3"></i> Pengajuan Cuti
                </a>
            </li>

            {{-- GROUP: AKUN --}}
            <!-- <li class="mt-3 px-3 text-uppercase small fw-bold" style="color: rgba(255, 255, 255, 0.5); font-size: 0.75rem;">
                Akun Saya
            </li>

            {{-- Profil --}}
            <li class="nav-item">
                <a href="{{ route('employee.profile') }}"
                    class="nav-link d-flex align-items-center {{ request()->routeIs('employee.profile') ? 'menu-aktif' : '' }}">
                    <i class="bi bi-person-circle me-3"></i> Profil & Akun
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