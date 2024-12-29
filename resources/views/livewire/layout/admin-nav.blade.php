<ul class="menu-inner py-1">
    <!-- Dashboards -->
    <li class="menu-item {{ Request::is('home') ? 'active' : '' }}">
        <a href="/home" class="menu-link">
            <i class="menu-icon tf-icons ri-home-smile-line"></i>
            <div data-i18n="Dashboards">Dashboards</div>
        </a>

    </li>

    <li class="menu-item {{ Request::is(['fields', 'fields/*']) ? 'active' : '' }}">
        <a href="{{ route('fields.index') }}" class="menu-link">
            <i class="menu-icon tf-icons ri-football-line"></i>
            <div data-i18n="Dashboards">Lapangan</div>
        </a>
    </li>


    <li class="menu-item {{ Request::is(['schedules', 'schedules/*']) ? 'active' : '' }}">
        <a href="{{ route('schedules.index') }}" class="menu-link">
            <i class="menu-icon tf-icons ri-money-dollar-box-line"></i>
            <div data-i18n="Dashboards">Jadwal Main</div>
        </a>
    </li>

    <li class="menu-item {{ Request::is(['settings', 'settings/*']) ? 'active' : '' }}">
        <a href="{{ route('settings.index') }}" class="menu-link">
            <i class="menu-icon tf-icons ri-layout-left-line"></i>
            <div data-i18n="Dashboards">Pengaturan</div>
        </a>
    </li>

    <li class="menu-item {{ Request::is(['transactions', 'transactions/*']) ? 'active' : '' }}">
        <a href="{{ route('transactions.index') }}" class="menu-link">
            <i class="menu-icon tf-icons ri-layout-left-line"></i>
            <div data-i18n="Dashboards">Transaksi</div>
        </a>
    </li>

    <li class="menu-item {{ Request::is(['reports', 'reports/*']) ? 'active' : '' }}">
        <a href="{{ route('reports.index') }}" class="menu-link">
            <i class="menu-icon tf-icons ri-layout-left-line"></i>
            <div data-i18n="Dashboards">Laporan Transaksi</div>
        </a>
    </li>



</ul>
