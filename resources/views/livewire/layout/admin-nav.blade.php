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


    <li class="menu-header mt-7">
        <span class="menu-header-text">Menus Example</span>
    </li>
    <!-- Apps -->

    <!-- Pages -->
    <li class="menu-item">
        <a href="/home" class="menu-link menu-toggle">
            <i class="menu-icon tf-icons ri-layout-left-line"></i>
            <div data-i18n="Account Settings">Account Settings</div>
        </a>
        <ul class="menu-sub">
            <li class="menu-item">
                <a href="pages-account-settings-account.html" class="menu-link">
                    <div data-i18n="Account">Account</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="pages-account-settings-notifications.html" class="menu-link">
                    <div data-i18n="Notifications">Notifications</div>
                </a>
            </li>
            <li class="menu-item">
                <a href="pages-account-settings-connections.html" class="menu-link">
                    <div data-i18n="Connections">Connections</div>
                </a>
            </li>
        </ul>
    </li>
    <!-- Components -->

</ul>
