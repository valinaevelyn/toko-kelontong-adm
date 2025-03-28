<header class="navbar sticky-top bg-gradient-custom flex-md-nowrap p-0 shadow" data-bs-theme="dark">
    <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3 fs-5 text-white fw-bold" href="#">AS MART</a>

    <ul class="navbar-nav flex-row d-md-none">
        <li class="nav-item text-nowrap">
            <button class="nav-link px-3 text-white btn-hover" type="button" data-bs-toggle="collapse"
                data-bs-target="#navbarSearch" aria-controls="navbarSearch" aria-expanded="false"
                aria-label="Toggle search">
                <i class="bi bi-search"></i>
            </button>
        </li>
        <li class="nav-item text-nowrap">
            <button class="nav-link px-3 text-white btn-hover" type="button" data-bs-toggle="offcanvas"
                data-bs-target="#sidebarMenu" aria-controls="sidebarMenu" aria-expanded="false"
                aria-label="Toggle navigation">
                <i class="bi bi-list"></i>
            </button>
        </li>
    </ul>

    <div id="navbarSearch" class="navbar-search w-100 collapse">
        <input class="form-control w-100 rounded-0 border-0 bg-light text-dark" type="text" placeholder="🔍 Search..."
            aria-label="Search">
    </div>
</header>

<style>
    /* Header Navbar */
    .navbar {
        background: linear-gradient(135deg, #0056b3 0%, #003f7f 100%);
        padding: 12px 15px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    /* Branding Style */
    .navbar-brand {
        font-size: 18px;
        font-weight: bold;
        letter-spacing: 0.5px;
    }

    /* Tombol Hover Efek Minimalis */
    .btn-hover {
        transition: background 0.3s ease-in-out, transform 0.1s;
        border-radius: 6px;
    }

    .btn-hover:hover {
        background: rgba(255, 255, 255, 0.15);
        transform: scale(1.05);
    }

    /* Search Bar */
    .navbar-search input {
        padding: 10px;
        font-size: 14px;
        border-radius: 5px;
        border: 1px solid #ddd;
        transition: all 0.3s;
    }

    .navbar-search input:focus {
        background: #fff;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.15);
        border: 1px solid #0056b3;
    }
</style>