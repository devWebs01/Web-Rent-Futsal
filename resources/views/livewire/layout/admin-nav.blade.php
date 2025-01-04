<ul class="menu-inner py-1">
    <!-- Dashboards -->
    <li class="menu-item {{ Request::is('home') ? 'active' : '' }}">
        <a href="/home" class="menu-link">
            <i class='menu-icon tf-icons bx bx-home-alt'></i>
            <div data-i18n="Dashboards">Dashboards</div>
        </a>

    </li>

    <li class="menu-item {{ Request::is(['fields', 'fields/*']) ? 'active' : '' }}">
        <a href="{{ route('fields.index') }}" class="menu-link">
            <i class='menu-icon tf-icons bx bxl-foursquare'></i>
            <div data-i18n="Dashboards">Lapangan</div>
        </a>
    </li>


    <li class="menu-item {{ Request::is(['schedules', 'schedules/*']) ? 'active' : '' }}">
        <a href="{{ route('schedules.index') }}" class="menu-link">
            <i class='menu-icon tf-icons bx bx-time'></i>
            <div data-i18n="Dashboards">Jadwal Main</div>
        </a>
    </li>

    <li class="menu-item {{ Request::is(['settings', 'settings/*']) ? 'active' : '' }}">
        <a href="{{ route('settings.index') }}" class="menu-link">
            <i class='menu-icon tf-icons bx bx-cog'></i>
            <div data-i18n="Dashboards">Pengaturan</div>
        </a>
    </li>

    <li class="menu-item {{ Request::is(['transactions', 'transactions/*']) ? 'active' : '' }}">
        <a href="{{ route('transactions.index') }}" class="menu-link">
            <i class='menu-icon tf-icons bx bx-home-alt'></i>
            <div data-i18n="Dashboards">Transaksi</div>
        </a>
    </li>

    <li class="menu-item {{ Request::is(['reports', 'reports/*']) ? 'active' : '' }}">
        <a href="{{ route('reports.index') }}" class="menu-link">
            <i class='menu-icon tf-icons bx bx-transfer'></i>
            <div data-i18n="Dashboards">Laporan Transaksi</div>
        </a>
    </li>

</ul>

{{-- <ul class="menu-sub">
    <li class="menu-item active">
        <a href="index.html" class="menu-link">
            <div class="text-truncate" data-i18n="Analytics">Analytics</div>
        </a>
    </li>
    <li class="menu-item">
        <a href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/html/vertical-menu-template/dashboards-crm.html"
            target="_blank" class="menu-link">
            <div class="text-truncate" data-i18n="CRM">CRM</div>
            <div class="badge rounded-pill bg-label-primary text-uppercase fs-tiny ms-auto">Pro
            </div>
        </a>
    </li>
    <li class="menu-item">
        <a href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/html/vertical-menu-template/app-ecommerce-dashboard.html"
            target="_blank" class="menu-link">
            <div class="text-truncate" data-i18n="eCommerce">eCommerce</div>
            <div class="badge rounded-pill bg-label-primary text-uppercase fs-tiny ms-auto">Pro
            </div>
        </a>
    </li>
    <li class="menu-item">
        <a href="https://demos.themeselection.com/sneat-bootstrap-html-admin-template/html/vertical-menu-template/app-logistics-dashboard.html"
            target="_blank" class="menu-link">
            <div class="text-truncate" data-i18n="Logistics">Logistics</div>
            <div class="badge rounded-pill bg-label-primary text-uppercase fs-tiny ms-auto">Pro
            </div>
        </a>
    </li>
    <li class="menu-item">
        <a href="app-academy-dashboard.html" target="_blank" class="menu-link">
            <div class="text-truncate" data-i18n="Academy">Academy</div>
            <div class="badge rounded-pill bg-label-primary text-uppercase fs-tiny ms-auto">Pro
            </div>
        </a>
    </li>
</ul> --}}
