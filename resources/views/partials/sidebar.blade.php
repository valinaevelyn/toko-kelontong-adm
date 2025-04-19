<div class="sidebar border-end bg-dark text-white col-md-3 col-lg-2 p-0" style="min-height: 100vh;">
    <div class="offcanvas-md offcanvas-end bg-dark text-white" tabindex="-1" id="sidebarMenu"
        aria-labelledby="sidebarMenuLabel">
        <div class="offcanvas-header border-bottom">
            <h5 class="offcanvas-title text-uppercase fw-bold" id="sidebarMenuLabel">AS MART</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                data-bs-target="#sidebarMenu" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body d-md-flex flex-column p-0 pt-3 overflow-y-auto">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 text-white @yield('dashboardActive') p-3"
                        aria-current="page" href="/dashboard">
                        <i class="bi bi-house-fill"></i>
                        Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 text-white p-3 @yield('penjualanActive')"
                        href="/penjualan">
                        <i class="bi bi-file-earmark"></i>
                        Penjualan
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 text-white p-3 @yield('itemActive')"
                        href="/item">
                        <i class="bi bi-cart"></i>
                        Item
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 text-white p-3 @yield('pembelianActive')"
                        href="/pembelian">
                        <i class="bi bi-people"></i>
                        Pembelian
                    </a>
                </li>

            </ul>

            <hr class="my-3 border-light">

            @can('supervisor')
                <ul class="nav flex-column mb-auto">
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2 text-white p-3 @yield('reportActive')"
                            href="/laporan-kas">
                            <i class="bi bi-bar-chart-line"></i>
                            Laporan Kas
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2 text-white p-3 @yield('reportActive')"
                            href="/laporan-bank">
                            <i class="bi bi-bar-chart-line"></i>
                            Laporan Bank
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2 text-white p-3 @yield('reportActive')"
                            href="/laporan-piutang">
                            <i class="bi bi-bar-chart-line"></i>
                            Laporan Piutang
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2 text-white p-3 @yield('reportActive')"
                            href="/laporan-utang">
                            <i class="bi bi-bar-chart-line"></i>
                            Laporan Utang
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2 text-white p-3 @yield('reportActive')"
                            href="/laporan-item">
                            <i class="bi bi-bar-chart-line"></i>
                            Laporan Item
                        </a>
                    </li>
                </ul>

                <hr class="my-3 border-light">
            @endcan

            <ul class="nav flex-column mb-auto">
                {{-- <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 text-white p-3" href="#">
                        <i class="bi bi-gear-wide-connected"></i>
                        Settings
                    </a>
                </li> --}}
                <li class="nav-item">
                    <a class="nav-link d-flex align-items-center gap-2 text-white p-3" href="/logout">
                        <i class="bi bi-door-closed"></i>
                        Sign out
                    </a>
                </li>
            </ul>
        </div>
    </div>
</div>

<style>
    /* Sidebar */
    .sidebar {
        background: #1f1f1f;
        /* Warna gelap solid */
        min-height: 100vh;
        transition: all 0.3s ease-in-out;
    }

    /* Styling untuk setiap item menu */
    .nav-link {
        transition: all 0.3s ease-in-out;
        border-radius: 5px;
        font-size: 15px;
        padding: 12px 15px;
        color: #ddd;
        /* Warna teks sedikit redup agar nyaman dilihat */
    }

    /* Hover efek minimalis */
    .nav-link:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #ffffff;
    }

    /* Menu aktif lebih tegas */
    .nav-link.active {
        background: #007bff;
        color: white;
        font-weight: 600;
        border-left: 4px solid #0056b3;
        /* Garis tepi untuk penekanan */
    }

    /* Header Sidebar */
    .offcanvas-header {
        background: #1f1f1f;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
</style>