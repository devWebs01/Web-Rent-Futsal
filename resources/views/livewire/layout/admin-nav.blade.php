<ul class="menu-inner py-1">
    <!-- Dashboards -->
    <li class="menu-item {{ Request::is('home') ? 'active' : '' }}">
        <a href="/home" class="menu-link">
            <i class='menu-icon tf-icons bx bx-home-alt'></i>
            <div data-i18n="Dashboards">Dashboards</div>
        </a>

    </li>

    <li class="menu-item {{ Request::is(['admin/users', 'admin/users/*', 'admin/customers']) ? 'open' : '' }}">
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

    <li
        class="menu-item {{ Request::is(['admin/fields', 'admin/fields/*', 'admin/schedules', 'admin/schedules/*']) ? 'open' : '' }}">
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

    <li
        class="menu-item {{ Request::is(['admin/settings', 'admin/settings/*', 'admin/schedules', 'admin/schedules/*']) ? 'open' : '' }}">
        <a href="javascript:void(0);" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons bx bx-cog"></i>
            <div class="text-truncate" data-i18n="Logistics">Pengaturan</div>
        </a>
        <ul class="menu-sub">
            <li class="menu-item">
                <a href="{{ route('settings.index') }}" class="menu-link">
                    <div class="text-truncate" data-i18n="Dashboard">Profil</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="{{ route('schedules.index') }}" class="menu-link">
                    <div class="text-truncate" data-i18n="Fleet">Rekening Pembayaran </div>
                </a>
            </li>
        </ul>
    </li>

    <li class="menu-item {{ Request::is(['admin/transactions', 'admin/transactions/*']) ? 'active' : '' }}">
        <a href="{{ route('transactions.index') }}" class="menu-link">
            <i class='menu-icon tf-icons bx bx-transfer'></i>
            <div data-i18n="Dashboards">Transaksi</div>
        </a>
    </li>

    <li class="menu-item {{ Request::is(['admin/reports', 'admin/reports/*']) ? 'active' : '' }}">
        <a href="{{ route('reports.index') }}" class="menu-link">
            <i class='menu-icon tf-icons bx bxs-report'></i>
            <div data-i18n="Dashboards">Laporan Transaksi</div>
        </a>
    </li>
</ul>
