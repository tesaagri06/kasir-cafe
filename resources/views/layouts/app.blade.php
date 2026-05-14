<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'CafePOS') | Kasir Cafe</title>
    {{-- Font: Poppins for body + logo --}}
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
<div class="app-container">

    {{-- ============================================================
         SIDEBAR
         ============================================================ --}}
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <div class="logo">
                <div class="logo-icon"><i class="fas fa-coffee"></i></div>
                <div class="logo-text">CafePOS</div>
            </div>
            <div class="sidebar-subtitle">
                {{ auth()->user()->isKasir() ? 'Kasir Edition' : 'Customer Edition' }}
            </div>
        </div>

        <nav class="nav-menu">
            @if(auth()->user()->isKasir() || auth()->user()->isAdmin())

                <div class="nav-item">
                    <a href="{{ route('dashboard') }}"
                       class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span class="nav-label">Dashboard</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('transaksi.create') }}"
                       class="nav-link {{ request()->routeIs('transaksi.create') ? 'active' : '' }}">
                        <i class="fas fa-cash-register"></i>
                        <span class="nav-label">Transaksi Baru</span>
                        <span class="nav-badge">New</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('menu.index') }}"
                       class="nav-link {{ request()->routeIs('menu.*') ? 'active' : '' }}">
                        <i class="fas fa-utensils"></i>
                        <span class="nav-label">Kelola Menu</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('kategori.index') }}"
                       class="nav-link {{ request()->routeIs('kategori.*') ? 'active' : '' }}">
                        <i class="fas fa-list-alt"></i>
                        <span class="nav-label">Kategori</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('laporan.index') }}"
                       class="nav-link {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                        <i class="fas fa-chart-pie"></i>
                        <span class="nav-label">Laporan</span>
                    </a>
                </div>

                @if(auth()->user()->isAdmin())
                <div class="nav-item">
                    <a href="{{ route('pengaturan.index') }}"
                       class="nav-link {{ request()->routeIs('pengaturan.*') ? 'active' : '' }}">
                        <i class="fas fa-cog"></i>
                        <span class="nav-label">Pengaturan</span>
                    </a>
                </div>
                @endif

            @else
                {{-- Customer role --}}
                <div class="nav-item">
                    <a href="{{ route('transaksi.create') }}"
                       class="nav-link {{ request()->routeIs('transaksi.create') ? 'active' : '' }}">
                        <i class="fas fa-utensils"></i>
                        <span class="nav-label">Menu & Pesan</span>
                    </a>
                </div>

                <div class="nav-item">
                    <a href="{{ route('transaksi.index') }}"
                       class="nav-link {{ request()->routeIs('transaksi.index') ? 'active' : '' }}">
                        <i class="fas fa-history"></i>
                        <span class="nav-label">Riwayat</span>
                    </a>
                </div>
            @endif
        </nav>

        <div class="sidebar-footer">
            <div class="user-profile">
                <div class="user-avatar">
                    {{ strtoupper(substr(auth()->user()->nama_lengkap, 0, 1)) }}
                </div>
                <div class="user-info">
                    <h4>{{ auth()->user()->nama_lengkap }}</h4>
                    <p>{{ ucfirst(auth()->user()->role) }}</p>
                </div>
            </div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-link" style="width:100%;background:none;border:none;cursor:pointer;color:rgba(255,255,255,0.75);">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="nav-label">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    {{-- Overlay (mobile) — klik di luar sidebar untuk tutup --}}
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    {{-- ============================================================
         MAIN CONTENT
         ============================================================ --}}
    <main class="main-content">

        {{-- Top Header --}}
        <header class="top-header">
            <div class="header-left">
                <h1>
                    <i class="fas @yield('page-icon', 'fa-home')"></i>
                    @yield('page-title', 'Dashboard')
                </h1>
            </div>
            <div class="header-right">
                @yield('header-actions')
            </div>
        </header>

        {{-- Content --}}
        <div class="content-container">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </div>

    </main>

    {{-- Mobile FAB toggle --}}
    <button class="mobile-menu-toggle" id="menuToggle" onclick="toggleSidebar()" aria-label="Toggle Menu">
        <i class="fas fa-bars"></i>
    </button>

</div>

<script>
    const sidebar  = document.getElementById('sidebar');
    const overlay  = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('menuToggle');

    function openSidebar() {
        sidebar.classList.add('open');
        overlay.style.display = 'block';
        toggleBtn.innerHTML = '<i class="fas fa-times"></i>';
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.style.display = 'none';
        toggleBtn.innerHTML = '<i class="fas fa-bars"></i>';
    }

    function toggleSidebar() {
        sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
    }

    // Auto-dismiss flash messages after 4 seconds
    document.querySelectorAll('.alert').forEach(el => {
        setTimeout(() => {
            el.style.transition = 'opacity 0.4s ease';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 400);
        }, 4000);
    });
</script>

@stack('scripts')
</body>
</html>