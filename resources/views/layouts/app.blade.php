<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'LaporBanjir') — BPBD Kabupaten Bandung</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        :root {
            --ink: #0F2A3D;
            --water: #0B5C7A;
            --water-dark: #084A63;
            --mist: #EEF3F6;
            --line: #D5E0E6;
            --warn: #B7791F;
            --alert: #B3261E;
            --ok: #2E7D4F;
            --bs-body-font-family: 'Public Sans', system-ui, -apple-system, 'Segoe UI', sans-serif;
            --bs-body-color: var(--ink);
            --bs-body-bg: var(--mist);
            --bs-link-color: var(--water);
            --bs-link-hover-color: var(--water-dark);
            --bs-border-color: var(--line);
        }
        body { min-height: 100vh; display: flex; flex-direction: column; }
        main { flex: 1; }
        h1, h2, h3, h4 { letter-spacing: -0.01em; }

        .btn-primary {
            --bs-btn-bg: var(--water); --bs-btn-border-color: var(--water);
            --bs-btn-hover-bg: var(--water-dark); --bs-btn-hover-border-color: var(--water-dark);
            --bs-btn-active-bg: #063B4F; --bs-btn-active-border-color: #063B4F;
            --bs-btn-focus-shadow-rgb: 11, 92, 122;
        }
        .btn-outline-primary {
            --bs-btn-color: var(--water); --bs-btn-border-color: var(--water);
            --bs-btn-hover-bg: var(--water); --bs-btn-hover-border-color: var(--water);
            --bs-btn-active-bg: var(--water-dark); --bs-btn-active-border-color: var(--water-dark);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--water); box-shadow: 0 0 0 .25rem rgba(11, 92, 122, .18);
        }
        .card { border-color: var(--line); border-radius: .5rem; }
        .card-header { background: #fff; font-weight: 600; border-bottom-color: var(--line); }

        /* Navbar */
        .site-nav { background: var(--ink); }
        .site-nav .navbar-brand { color: #fff; font-weight: 700; line-height: 1.1; }
        .site-nav .navbar-brand small { display: block; font-weight: 400; font-size: .72rem; color: #9DB6C4; }
        .site-nav .nav-link { color: #CFE0EA; }
        .site-nav .nav-link:hover, .site-nav .nav-link:focus { color: #fff; }
        .site-nav .nav-link.active { color: #fff; box-shadow: inset 0 -2px 0 #6FC3E0; }
        .site-nav .navbar-toggler { border-color: #3C5A6E; }
        .site-nav .navbar-toggler-icon { filter: invert(1); }
        .site-nav form { margin: 0; }
        .site-nav button.nav-link { cursor: pointer; }

        .site-footer { color: #5C7585; font-size: .85rem; }
        .page-title { font-weight: 700; font-size: 1.6rem; }

        /* Badge tingkat genangan & status */
        .tag { display: inline-block; padding: .15rem .55rem; border-radius: 999px; font-size: .78rem; font-weight: 600; }
        .tag-rendah { background: #E3F1E8; color: var(--ok); }
        .tag-sedang { background: #FBEFD5; color: var(--warn); }
        .tag-tinggi { background: #F9DEDC; color: var(--alert); }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg site-nav">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                <i class="fas fa-water me-1"></i>LaporBanjir
                <small>BPBD Kabupaten Bandung</small>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Buka menu">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    {{-- Menu untuk semua pengunjung: warga tidak perlu login untuk melapor --}}
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('laporan.form') ? 'active' : '' }}" href="{{ route('laporan.form') }}">
                            <i class="fas fa-pen-to-square me-1"></i>Kirim Laporan
                        </a>
                    </li>
                </ul>

                <ul class="navbar-nav ms-auto">
                    @auth
                        {{-- Menu petugas: tampil hanya setelah login --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('laporan.index') ? 'active' : '' }}" href="{{ route('laporan.index') }}">
                                <i class="fas fa-list-check me-1"></i>Daftar Laporan
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('profil') ? 'active' : '' }}" href="{{ route('profil') }}">
                                <i class="fas fa-user me-1"></i>Profil Petugas
                            </a>
                        </li>
                        <li class="nav-item">
                            {{-- Logout memakai form POST + @csrf --}}
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="nav-link">
                                    <i class="fas fa-right-from-bracket me-1"></i>Logout
                                </button>
                            </form>
                        </li>
                    @else
                        {{-- Menu untuk tamu (belum login) --}}
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">
                                <i class="fas fa-right-to-bracket me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('register') ? 'active' : '' }}" href="{{ route('register') }}">
                                <i class="fas fa-user-plus me-1"></i>Register
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-4">
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="container pb-4 site-footer">
        Darurat banjir? Hubungi BPBD Kabupaten Bandung atau layanan darurat 112.
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
