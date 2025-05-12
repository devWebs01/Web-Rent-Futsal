<ul class="menu-inner py-1">
    <!-- Dashboards -->
    <li class="menu-item {{ Route::is('home') ? 'active' : '' }}">
        <a href="/home" class="menu-link">
            <i class='menu-icon tf-icons bx bx-home-alt'></i>
            <div data-i18n="Dashboards">Dashboards</div>
        </a>

    </li>

    <li class="menu-item {{ Route::is(['users.index', 'customers']) ? 'open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-user"></i>
            <div class="text-truncate" data-i18n="Logistics">Pengguna</div>
        </a>
        <ul class="menu-sub">
            <li class="menu-item">
                <a href="{{ route('users.index') }}" class="menu-link">
                    <div class="text-truncate" data-i18n="Dashboard">Admin</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('customers') }}" class="menu-link">
                    <div class="text-truncate" data-i18n="Fleet">Pelanggan </div>
                </a>
            </li>
        </ul>
    </li>

    <li class="menu-item {{ Route::is(['fields.index', 'schedules.index']) ? 'open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bxl-foursquare"></i>
            <div class="text-truncate" data-i18n="Logistics">Futsal</div>
        </a>
        <ul class="menu-sub">
            <li class="menu-item">
                <a href="{{ route('fields.index') }}" class="menu-link">
                    <div class="text-truncate" data-i18n="Dashboard">Lapangan</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('schedules.index') }}" class="menu-link">
                    <div class="text-truncate" data-i18n="Fleet">Jadwal </div>
                </a>
            </li>
        </ul>
    </li>

    <li class="menu-item {{ Route::is(['users.index', 'blogs.index']) ? 'open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-news"></i>
            <div class="text-truncate" data-i18n="Logistics">Informasi</div>
        </a>
        <ul class="menu-sub">
            <li class="menu-item">
                <a href="{{ route('galleries.index') }}" class="menu-link">
                    <div class="text-truncate" data-i18n="Dashboard">Galeri</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('blogs.index') }}" class="menu-link">
                    <div class="text-truncate" data-i18n="Fleet">Blog </div>
                </a>
            </li>
        </ul>
    </li>


    <li class="menu-item {{ Route::is(['settings.index']) ? 'active' : '' }}">
        <a href="{{ route('settings.index') }}" class="menu-link">
            <i class="menu-icon tf-icons bx bx-cog"></i>
            <div data-i18n="Dashboards">Profil Website</div>
        </a>
    </li>

    <li class="menu-item {{ Route::is(['transactions.index']) ? 'active' : '' }}">
        <a href="{{ route('transactions.index') }}" class="menu-link">
            <i class='menu-icon tf-icons bx bx-transfer'></i>
            <div data-i18n="Dashboards">Transaksi</div>
        </a>
    </li>

    <li class="menu-item {{ Route::is(['reports.index']) ? 'active' : '' }}">
        <a href="{{ route('reports.index') }}" class="menu-link">
            <i class='menu-icon tf-icons bx bxs-report'></i>
            <div data-i18n="Dashboards">Laporan Transaksi</div>
        </a>
    </li>
</ul>
