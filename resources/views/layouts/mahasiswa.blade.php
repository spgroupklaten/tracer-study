<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="Admin - Alumni & Career Center UMKlaten">
    <title>{{ $title ?? 'Dashboard' }} - Alumni & Career</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- AdminLTE v4 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0/dist/css/adminlte.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    @stack('styles')
</head>
<body class="layout-fixed sidebar-expand-lg">
    <div class="app-wrapper">
        <!-- Navbar -->
        <nav class="app-header navbar navbar-expand" style="background-color: #ffffff; border-bottom: 1px solid #e5e7eb;">
            <div class="container-fluid px-3">
                <!-- Left navbar links -->
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                            <i class="fas fa-bars"></i>
                        </a>
                    </li>
                    <li class="nav-item d-none d-md-inline-block">
                        <a href="{{ route('mahasiswa.dashboard') }}" class="nav-link" style="color: #6b7280; font-size: 0.875rem;">Dashboard</a>
                    </li>
                </ul>

                <!-- Right navbar links -->
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle p-1" data-bs-toggle="dropdown">
                            <div class="user-panel d-flex align-items-center gap-2">
                                <div class="image">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 2.625rem; height: 2.625rem; background-color: #dcfce7;">
                                        <span style="color: #16a34a; font-weight: 600; font-size: 1rem;">{{ substr(Auth::user()->name, 0, 1) }}</span>
                                    </div>
                                </div>
                                <div class="info d-none d-xl-inline-block">
                                    <span style="font-size: 0.9375rem; font-weight: 600; color: #111827; display: block; line-height: 1.2;">{{ Auth::user()->name }}</span>
                                    <span style="font-size: 0.75rem; color: #6b7280; display: block;">Mahasiswa</span>
                                </div>
                                <i class="fas fa-chevron-down text-muted ms-1" style="font-size: 0.6875rem;"></i>
                            </div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <li>
                                <div class="px-3 py-2 border-bottom">
                                    <p class="mb-0" style="font-size: 0.875rem; color: #1f2937; font-weight: 500;">{{ Auth::user()->name }}</p>
                                    <p class="mb-0" style="font-size: 0.75rem; color: #6b7280;">{{ Auth::user()->email }}</p>
                                </div>
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger border-0 w-100 text-start" style="font-size: 0.875rem;">
                                        <i class="fas fa-sign-out-alt me-2"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Sidebar - Alumni & Career Theme -->
        <aside class="app-sidebar sidebar-expand-lg shadow" style="background: linear-gradient(180deg, #1363c6 0%, #1a365d 100%) !important;">
            <nav class="mt-2 py-1">
                <ul class="nav nav-pills nav-sidebar flex-column" data-lte-menu="treeview" role="menu" data-accordion="false">
                    <!-- Dashboard -->
                    <li class="nav-item">
                        <a href="{{ route('mahasiswa.dashboard') }}" class="nav-link {{ request()->routeIs('mahasiswa.dashboard') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-chart-pie"></i>
                            <p class="mb-0">Dashboard</p>
                        </a>
                    </li>

                    <!-- Menu Mahasiswa -->
                    <li class="nav-header" style="color: rgba(255,255,255,0.4); font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 0.75rem 0.75rem 0.25rem;">Menu</li>
                    <li class="nav-item">
                        <a href="{{ route('mahasiswa.profile.index') }}" class="nav-link {{ request()->routeIs('mahasiswa.profile.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-user-circle"></i>
                            <p class="mb-0">Profil Saya</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('mahasiswa.tracer-study.index') }}" class="nav-link {{ request()->routeIs('mahasiswa.tracer-study.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-clipboard-list"></i>
                            <p class="mb-0">Tracer Study</p>
                        </a>
                    </li>

                    <!-- Informasi -->
                    <li class="nav-header" style="color: rgba(255,255,255,0.4); font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.05em; padding: 0.75rem 0.75rem 0.25rem;">Informasi</li>
                    <li class="nav-item">
                        <a href="{{ route('home') }}" class="nav-link">
                            <i class="nav-icon fas fa-home"></i>
                            <p class="mb-0">Beranda</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('alumni.statistics') }}" class="nav-link">
                            <i class="nav-icon fas fa-chart-bar"></i>
                            <p class="mb-0">Statistik</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="app-main">
            <div class="app-content" style="padding: 2rem;">
                @yield('content')
            </div>
        </main>

        <!-- Footer -->
        <footer class="app-footer" style="background-color: #ffffff; border-top: 1px solid #e5e7eb; padding: 0.75rem 1.25rem; font-size: 0.8rem; color: #6b7280;">
            <div class="text-center">
                <strong style="font-weight: 500;">Copyright &copy; {{ date('Y') }} Alumni & Career Center - Universitas Muhammadiyah Klaten</strong>
            </div>
        </footer>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- AdminLTE v4 JS -->
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@4.0.0/dist/js/adminlte.min.js"></script>

    <style>
        /* Base font & background */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f4f6f9;
            font-size: 0.875rem;
        }

        /* Sidebar - Icon and Text Aligned */
        .app-sidebar .nav-link {
            display: flex !important;
            align-items: center !important;
            color: rgba(255, 255, 255, 0.85) !important;
            padding: 0.5rem 0.75rem !important;
            border-radius: 0.25rem;
            margin: 0.125rem 0.5rem;
            font-size: 0.85rem;
            line-height: 1.4;
            transition: all 0.15s;
        }

        .app-sidebar .nav-link:hover {
            background-color: rgba(255, 255, 255, 0.1) !important;
            color: #ffffff !important;
        }

        .app-sidebar .nav-link.active,
        .app-sidebar .nav-link.active:hover {
            background-color: rgba(255, 255, 255, 0.15) !important;
            color: #ffffff !important;
        }

        .app-sidebar .nav-icon {
            display: flex !important;
            align-items: center !important;
            justify-content: center;
            color: rgba(255, 255, 255, 0.7) !important;
            width: 1.75rem !important;
            height: 1.75rem;
            margin-right: 0.5rem;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .app-sidebar .nav-link:hover .nav-icon,
        .app-sidebar .nav-link.active .nav-icon {
            color: #ffffff !important;
        }

        .app-sidebar .nav-link p {
            display: flex !important;
            align-items: center;
            margin: 0 !important;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Sidebar collapse */
        .sidebar-collapse .app-sidebar {
            width: 0 !important;
            overflow: hidden;
        }
        .sidebar-collapse .app-main {
            margin-left: 0 !important;
        }

        /* Content area */
        .app-content {
            background-color: #f4f6f9;
        }

        /* Page header */
        .app-content-header {
            background-color: #ffffff;
            border-bottom: 1px solid #e9ecef;
        }

        /* Cards - compact */
        .card {
            background-color: #ffffff;
            border-radius: 0.375rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            border: 1px solid #e9ecef;
            margin-bottom: 0;
        }

        .card-header {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #e9ecef;
            background-color: #ffffff;
            font-size: 0.9rem;
            font-weight: 600;
            color: #212529;
        }

        .card-header:first-child {
            border-radius: 0.375rem 0.375rem 0 0;
        }

        .card-body {
            padding: 1rem;
        }

        .card-footer {
            padding: 0.75rem 1rem;
            border-top: 1px solid #e9ecef;
            background-color: #f8f9fa;
        }

        /* Buttons - compact */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.375rem 0.75rem;
            font-size: 0.8125rem;
            font-weight: 500;
            border-radius: 0.25rem;
            transition: all 0.15s;
            gap: 0.375rem;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
        }

        .btn-primary {
            background-color: #2563eb;
            color: #ffffff;
            border: none;
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
        }

        .btn-success {
            background-color: #16a34a;
            color: #ffffff;
            border: none;
        }

        .btn-success:hover {
            background-color: #15803d;
        }

        /* Form controls - compact */
        .form-control {
            display: block;
            width: 100%;
            padding: 0.375rem 0.625rem;
            font-size: 0.8125rem;
            line-height: 1.5;
            color: #212529;
            background-color: #ffffff;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            transition: border-color 0.15s, box-shadow 0.15s;
            height: 2rem;
        }

        /* Stats cards - compact */
        .stats-card {
            background: #ffffff;
            border-radius: 0.375rem;
            padding: 1rem;
            border: 1px solid #e9ecef;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .stats-card:hover {
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
        }

        /* Navbar styling */
        .app-header .nav-link {
            color: #6b7280;
        }

        .app-header .nav-link:hover {
            color: #1f2937;
            background-color: #f3f4f6;
        }

        /* User panel */
        .user-panel .image {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Dropdown */
        .dropdown-menu {
            font-size: 0.8125rem;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .dropdown-item {
            padding: 0.5rem 1rem;
            font-size: 0.8125rem;
        }

        /* Compact grid gaps */
        .gap-1 { gap: 0.25rem; }
        .gap-2 { gap: 0.5rem; }
        .gap-3 { gap: 0.75rem; }
        .gap-4 { gap: 1rem; }
    </style>

    @stack('scripts')
</body>
</html>
