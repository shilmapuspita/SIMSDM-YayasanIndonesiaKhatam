<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', config('app.name', 'SIMSDM'))</title>

    {{-- Vite --}}
    @vite('resources/css/app.css')

    {{-- Bootstrap & Icons --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    {{-- Custom Style --}}
    <style>
        :root {
            --white: #ffffff;
            --blue: #000080;
            --orange: #f77042;
            --soft-bg: #f5f7fb;
        }

        body {
            background-color: var(--soft-bg);
            font-family: 'Poppins', sans-serif;
        }

        /* Sidebar */
        .sidebar {
            width: 260px;
            background: var(--blue);
            color: #fff;
            border-radius: 18px;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, .85);
            border-radius: 12px;
            padding: 11px 16px;
            transition: .2s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background-color: var(--orange);
            color: #fff;
        }

        /* Topbar */
        .topbar {
            background: var(--white);
            border-radius: 18px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, .05);
        }

        /* Card */
        .card {
            border: none;
            border-radius: 18px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, .06);
        }

        /* TOMBOL TAMBAH */
        .btn-add {
            background-color: var(--blue);
            border-color: var(--blue);
            color: var(--white);
            padding: 6px 16px;
            font-weight: 500;
        }

        .btn-add:hover {
            background-color: #000066;
            border-color: #000066;
            color: var(--white);
        }

        /* DETAIL */
        .btn-detail {
            background-color: var(--blue);
            color: var(--white);
            border: none;
        }

        .btn-detail:hover {
            background-color: #000066;
            color: var(--white);
        }

        /* EDIT */
        .btn-edit {
            background-color: var(--orange);
            color: var(--white);
            border: none;
        }

        .btn-edit:hover {
            background-color: #e65f35;
            color: var(--white);
        }

        /* DELETE */
        .btn-delete {
            background-color: #dc3545;
            color: var(--white);
            border: none;
        }

        .btn-delete:hover {
            background-color: #bb2d3b;
            color: var(--white);
        }

        /* BIAR RAPI */
        table td,
        table th {
            vertical-align: middle;
        }
    </style>
</head>

<body>

    <div class="d-flex min-vh-100 gap-3 p-3">

        {{-- SIDEBAR (ROLE BASED) --}}
        @auth
        @if(auth()->user()->role === 'admin')
        @include('layouts.partials.sidebar-admin')
        @elseif(auth()->user()->role === 'hrd')
        @include('layouts.partials.sidebar-hrd')
        @elseif(auth()->user()->role === 'employee')
        @include('layouts.partials.sidebar-employee')
        @endif
        @endauth

        {{-- MAIN AREA --}}
        <div class="flex-grow-1 d-flex flex-column gap-3">

            {{-- TOPBAR --}}
            <div class="topbar px-4 py-3 d-flex align-items-center">
                <h5 class="mb-0 fw-semibold text-dark">
                    @yield('page-title', 'Dashboard')
                </h5>
            </div>

            {{-- PAGE CONTENT --}}
            <main class="flex-grow-1">
                @yield('content')
            </main>

            {{-- FOOTER --}}
            <footer class="text-center small pb-3 text-muted">
                © {{ date('Y') }} Yayasan Indonesia Khatam
            </footer>

        </div>
    </div>

    {{-- Scripts --}}
    @vite('resources/js/app.js')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>