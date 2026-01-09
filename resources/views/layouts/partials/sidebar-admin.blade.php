<aside class="sidebar d-flex flex-column">

    {{-- BRAND --}}
    <div class="p-4 border-bottom border-light border-opacity-25 text-center">
        <div class="bg-white p-2 rounded-circle d-inline-block shadow-sm">
            <img src="{{ asset('assets/img/logo.png') }}"
                alt="Logo"
                style="width: 50px; height: 50px; object-fit: contain;">
        </div>

        <h6 class="text-white-55 d-block">Yayasan Indonesia Khatam</h6>
    </div>

    {{-- MENU --}}
    <nav class="flex-grow-1 p-3">
        <ul class="nav flex-column gap-1">

            <!-- <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li> -->

            <!-- <li class="mt-3 text-uppercase small fw-semibold text-white-50 px-3">
                Manajemen User
            </li> -->

            <!-- <li class="nav-item">
                <a href="{{ route('admin.users.index') }}"
                    class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                    <i class="bi bi-people me-2"></i> Data User
                </a>
            </li> -->

            <!-- <li class="nav-item">
                <a href="{{ route('admin.roles.index') }}"
                    class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                    <i class="bi bi-shield-lock me-2"></i> Role & Hak Akses
                </a>
            </li> -->

            <li class="mt-3 text-uppercase small fw-semibold text-white-50 px-3">
                Data SDM
            </li>

            <li class="nav-item">
                <a href="{{ route('karyawan.index') }}"
                    class="nav-link {{ request()->routeIs('admin.employees.*') ? 'active' : '' }}">
                    <i class="bi bi-person-badge me-2"></i> Data Pegawai
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('absensi.index') }}"
                    class="nav-link {{ request()->routeIs('admin.attendances.*') ? 'active' : '' }}">
                    <i class="bi bi-calendar-check me-2"></i> Absensi
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('cuti.index') }}"
                    class="nav-link {{ request()->routeIs('admin.leaves.*') ? 'active' : '' }}">
                    <i class="bi bi-journal-text me-2"></i> Cuti
                </a>
            </li>

            <!-- <li class="mt-3 text-uppercase small fw-semibold text-white-50 px-3">
                Monitoring Sistem
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.logs.index') }}"
                    class="nav-link {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}">
                    <i class="bi bi-activity me-2"></i> Log Aktivitas
                </a>
            </li> -->

            <!-- <li class="mt-3 text-uppercase small fw-semibold text-white-50 px-3">
                Pengaturan
            </li>

            <li class="nav-item">
                <a href="{{ route('admin.settings') }}"
                    class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                    <i class="bi bi-gear me-2"></i> Pengaturan Sistem
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