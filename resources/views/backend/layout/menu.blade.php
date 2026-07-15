<div class="app-header-menu app-header-mobile-drawer align-items-stretch" data-kt-drawer="true"
    data-kt-drawer-name="app-header-menu" data-kt-drawer-activate="{default: true, lg: false}"
    data-kt-drawer-overlay="true" data-kt-drawer-width="250px" data-kt-drawer-direction="start"
    data-kt-drawer-toggle="#kt_app_header_menu_toggle" data-kt-swapper="true"
    data-kt-swapper-mode="{default: 'append', lg: 'prepend'}"
    data-kt-swapper-parent="{default: '#kt_app_body', lg: '#kt_app_header_wrapper'}">
    <!--begin::Menu-->
    <div class="menu menu-rounded menu-active-bg menu-state-primary menu-column menu-lg-row menu-title-gray-700 menu-icon-gray-500 menu-arrow-gray-500 menu-bullet-gray-500 my-5 my-lg-0 align-items-stretch fw-semibold px-2 px-lg-0"
        id="kt_app_header_menu" data-kt-menu="true">

        <div
            class="menu-item menu-here-bg me-0 me-lg-2 menu-hover-bg menu-hover-bg-warning {{ request()->routeIs('dashboard') ? 'here show ' : '' }}">
            <a href="{{ route('dashboard') }}"
                class="menu-link px-4 {{ request()->routeIs('dashboard') ? 'active ' : '' }}">

                <span class="menu-title">Dashboards</span>
            </a>
        </div>

        @canany(['skpd.list'])
        <!--begin:Menu item-->
        <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="bottom-start"
            class="menu-item menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2">
            <!--begin:Menu link-->
            <span class="menu-link py-3  {{ request()->routeIs('skpd.index') ? 'active ' : '' }}">
                <span class="menu-title">Intansi</span>
                <span class="menu-arrow d-lg-none">
                </span>
            </span>
            <!--end:Menu link-->
            <!--begin:Menu sub-->
            <div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown px-lg-2 py-lg-4 w-lg-200px">
                @can('skpd.list')
                <!--begin:Menu item-->
                <div class="menu-item {{ request()->routeIs('skpd.index') ? 'here show ' : '' }}">
                    <!--begin:Menu link-->
                    <a class="menu-link py-3 " href="{{ route('skpd.index') }}">
                        <span class="menu-icon">
                            <i class="ki-outline ki-virus fs-2"></i>
                        </span>
                        <span class="menu-title">Intansi Management</span>
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->
                @endcan
            </div>
            <!--end:Menu sub-->
        </div>
        @endcanany

        @canany(['antrian.list','antrian.call'])
        <!--begin:Menu item-->
        <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
            data-kt-menu-placement="bottom-start"
            class="menu-item menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2">

            <!--begin:Menu link-->
            <span class="menu-link py-3 {{ request()->routeIs('antrian.*') ? 'active' : '' }}">
                <span class="menu-title">Antrian</span>
                <span class="menu-arrow d-lg-none"></span>
            </span>
            <!--end:Menu link-->

            <!--begin:Menu sub-->
            <div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown px-lg-2 py-lg-4 w-lg-225px">
                {{-- ================= MANAJEMEN ANTRIAN ================= --}}
                @can('antrian.list')
                <div class="menu-item {{ request()->routeIs('antrian.index') ? 'here show' : '' }}">
                    <a class="menu-link py-3" href="{{ route('antrian.index') }}">
                        <span class="menu-icon">
                            <i class="ki-outline ki-notification-on fs-2"></i>
                        </span>
                        <span class="menu-title">Panggilan Antrian</span>
                    </a>
                </div>
                @endcan

            </div>
            <!--end:Menu sub-->
        </div>
        <!--end:Menu item-->
        @endcanany

        <!--end:Menu item-->
        @canany(['user.list', 'role.list'])
        <!--begin:Menu item-->
        <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="bottom-start"
            class="menu-item menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2">
            <!--begin:Menu link-->
            <span class="menu-link py-3  {{ request()->routeIs('users.index','roles.index') ? 'active ' : '' }}">
                <span class="menu-title">Resources</span>
                <span class="menu-arrow d-lg-none">
                </span>
            </span>
            <!--end:Menu link-->
            <!--begin:Menu sub-->
            <div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown px-lg-2 py-lg-4 w-lg-210px">
                @can('user.list')
                <!--begin:Menu item-->
                <div class="menu-item {{ request()->routeIs('users.index') ? 'here show ' : '' }}">
                    <!--begin:Menu link-->
                    <a class="menu-link py-3 " href="{{ route('users.index') }}">
                        <span class="menu-icon">
                            <i class="ki-outline ki-rocket fs-2"></i>
                        </span>
                        <span class="menu-title">User Management</span>
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->
                @endcan
                @can('role.list')
                <!--begin:Menu item-->
                <div class="menu-item {{ request()->routeIs('roles.index') ? 'here show ' : '' }}">
                    <!--begin:Menu link-->
                    <a class="menu-link py-3" href="{{ route('roles.index') }}">
                        <span class="menu-icon">
                            <i class="ki-outline ki-code fs-2"></i>
                        </span>
                        <span class="menu-title">Role Management</span>
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->
                @endcan
                @can('loket.list')
                <!--begin:Menu item-->
                <div class="menu-item {{ request()->routeIs('loket.index') ? 'here show ' : '' }}">
                    <!--begin:Menu link-->
                    <a class="menu-link py-3" href="{{ route('loket.index') }}">
                        <span class="menu-icon">
                            <i class="ki-outline ki-logistic fs-2"></i>
                        </span>
                        <span class="menu-title">Layanan Management</span>
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->
                @endcan

                @can('layanan_skm.list')
                <!--begin:Menu item-->
                <div class="menu-item {{ request()->routeIs('layanan-skm.index') ? 'here show ' : '' }}">
                    <!--begin:Menu link-->
                    <a class="menu-link py-3" href="{{ route('layanan-skm.index') }}">
                        <span class="menu-icon">
                            <i class="ki-outline ki-setting-2 fs-2"></i>
                        </span>
                        <span class="menu-title">Layanan SKM</span>
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->
                @endcan

                @can('skm.list')
                <!--begin:Menu item-->
                <div class="menu-item {{ request()->routeIs('master.skm.index') ? 'here show ' : '' }}">
                    <!--begin:Menu link-->
                    <a class="menu-link py-3 " href="{{ route('master.skm.index') }}">
                        <span class="menu-icon">
                            <i class="ki-outline ki-chart-line-star fs-2"></i>
                        </span>
                        <span class="menu-title">Manajemen SKM</span>
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->
                @endcan
            </div>
            <!--end:Menu sub-->
        </div>
        <!--end:Menu item-->
        @endcanany

        @if (auth()->user()->hasRole('Superadmin') || auth()->user()->skpd_id)
        <!--begin:Menu item Antrian Online-->
        <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="bottom-start"
            class="menu-item menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2">
            <!--begin:Menu link-->
            <span
                class="menu-link py-3 {{ request()->routeIs('antrian-online-list.*', 'kalender.*', 'scan.*', 'form-persyaratan.*', 'landing-setting.*') ? 'active ' : '' }}">
                <span class="menu-title">Antrian Online</span>
                <span class="menu-arrow d-lg-none"></span>
            </span>
            <!--end:Menu link-->
            <!--begin:Menu sub-->
            <div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown px-lg-2 py-lg-4 w-lg-225px">
                <!--begin:Menu item-->
                <div class="menu-item {{ request()->routeIs('antrian-online-list.index') ? 'here show ' : '' }}">
                    <a class="menu-link py-3" href="{{ route('antrian-online-list.index') }}">
                        <span class="menu-icon">
                            <i class="ki-outline ki-people fs-2"></i>
                        </span>
                        <span class="menu-title">Daftar Antrian Online</span>
                    </a>
                </div>
                <!--end:Menu item-->
                <!--begin:Menu item-->
                <div class="menu-item {{ request()->routeIs('kalender.*') ? 'here show ' : '' }}">
                    <a class="menu-link py-3" href="{{ route('kalender.index') }}">
                        <span class="menu-icon">
                            <i class="ki-outline ki-calendar fs-2"></i>
                        </span>
                        <span class="menu-title">Kalender Antrian</span>
                    </a>
                </div>
                <!--end:Menu item-->
                <!--begin:Menu item-->
                <div class="menu-item {{ request()->routeIs('scan.*') ? 'here show ' : '' }}">
                    <a class="menu-link py-3" href="{{ route('scan.index') }}">
                        <span class="menu-icon">
                            <i class="ki-outline ki-scan-barcode fs-2"></i>
                        </span>
                        <span class="menu-title">Scan Antrean</span>
                    </a>
                </div>
                <!--end:Menu item-->
                @if (auth()->user()->hasRole('Superadmin'))
                <!--begin:Menu item-->
                <div class="menu-item {{ request()->routeIs('form-persyaratan.*') ? 'here show ' : '' }}">
                    <a class="menu-link py-3" href="{{ route('form-persyaratan.index') }}">
                        <span class="menu-icon">
                            <i class="ki-outline ki-document fs-2"></i>
                        </span>
                        <span class="menu-title">Form Persyaratan</span>
                    </a>
                </div>
                <!--end:Menu item-->
                <!--begin:Menu item-->
                <div class="menu-item {{ request()->routeIs('landing-setting.*') ? 'here show ' : '' }}">
                    <a class="menu-link py-3" href="{{ route('landing-setting.index') }}">
                        <span class="menu-icon">
                            <i class="ki-outline ki-gear fs-2"></i>
                        </span>
                        <span class="menu-title">Landing & Footer</span>
                    </a>
                </div>
                <!--end:Menu item-->
                @endif
            </div>
            <!--end:Menu sub-->
        </div>
        <!--end:Menu item Antrian Online-->
        @endif

        @if (auth()->user()->hasRole('Superadmin'))
        <!--begin:Menu item Display TV-->
        <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="bottom-start"
            class="menu-item menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2">
            <!--begin:Menu link-->
            <span class="menu-link py-3 {{ request()->routeIs('display-setting.*', 'announcement.*') ? 'active ' : '' }}">
                <span class="menu-title">Display TV</span>
                <span class="menu-arrow d-lg-none"></span>
            </span>
            <!--end:Menu link-->
            <!--begin:Menu sub-->
            <div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown px-lg-2 py-lg-4 w-lg-225px">
                <!--begin:Menu item-->
                <div class="menu-item {{ request()->routeIs('display-setting.*') ? 'here show ' : '' }}">
                    <a class="menu-link py-3" href="{{ route('display-setting.index') }}">
                        <span class="menu-icon">
                            <i class="ki-outline ki-screen fs-2"></i>
                        </span>
                        <span class="menu-title">Display Monitor TV</span>
                    </a>
                </div>
                <!--end:Menu item-->
                <!--begin:Menu item-->
                <div class="menu-item {{ request()->routeIs('announcement.*') ? 'here show ' : '' }}">
                    <a class="menu-link py-3" href="{{ route('announcement.index') }}">
                        <span class="menu-icon">
                            <i class="ki-outline ki-microphone-2 fs-2"></i>
                        </span>
                        <span class="menu-title">Pengumuman Suara TV</span>
                    </a>
                </div>
                <!--end:Menu item-->
            </div>
            <!--end:Menu sub-->
        </div>
        <!--end:Menu item Display TV-->
        @endif

        <!--begin:Menu item-->
        <div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="bottom-start"
            class="menu-item menu-lg-down-accordion menu-sub-lg-down-indention me-0 me-lg-2">
            <!--begin:Menu link-->
            <span class="menu-link py-3  {{ request()->routeIs('log-activity.index') ? 'active ' : '' }}">
                <span class="menu-title">Help</span>
                <span class="menu-arrow d-lg-none">
                </span>
            </span>
            <!--end:Menu link-->
            <!--begin:Menu sub-->
            <div class="menu-sub menu-sub-lg-down-accordion menu-sub-lg-dropdown px-lg-2 py-lg-4 w-lg-200px">
                <!--begin:Menu item-->
                <div class="menu-item {{ request()->routeIs('log-activity.index') ? 'here show ' : '' }}">
                    <!--begin:Menu link-->
                    <a class="menu-link py-3 " href="{{ route('log-activity.index') }}">
                        <span class="menu-icon">
                            <i class="ki-outline ki-rocket fs-2"></i>
                        </span>
                        <span class="menu-title">Log Activity</span>
                    </a>
                    <!--end:Menu link-->
                </div>
                <!--end:Menu item-->
                <!--begin:Menu item-->
                <!--end:Menu item-->
            </div>
            <!--end:Menu sub-->
        </div>
        <!--end:Menu item-->


        <!-- <div class="menu-item menu-here-bg me-0 me-lg-2 menu-hover-bg menu-hover-bg-warning">
            <a class="menu-link px-4">

                <span class="menu-title">Configuration</span>
            </a>
        </div> -->
    </div>
    <!--end::Menu-->
</div>
