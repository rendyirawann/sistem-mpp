<div class="app-navbar flex-shrink-0">
    <!--begin::Notifications-->
    <!--end::Notifications-->

    <!--begin::User menu-->
    <div class="app-navbar-item ms-3 ms-lg-9" id="kt_header_user_menu_toggle">
        <!--begin::Menu wrapper-->
        <div class="d-flex align-items-center" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
            data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
            <!--begin:Info-->
            <div class="text-end d-none d-sm-flex flex-column justify-content-center me-3">
                <span class="text-gray-500 fs-8 fw-bold">Selamat Datang</span>
                <a
                    class="text-gray-800 text-hover-primary fs-7 fw-bold d-block navbar-name">{{ ucwords(strtolower(Auth::user()->name)) }}</a>
            </div>
            <!--end:Info-->
            <!--begin::User-->
            <div class="symbol symbol-40px me-5">
    @if (Auth::user()->avatar)
        <img class="navbar-avatar-img" src="{{ asset('storage/user/avatar/' . Auth::user()->avatar) }}"
            alt="{{ Auth::user()->name }}" />
    @else
        <div class="symbol-label fs-3 bg-light-primary text-primary navbar-avatar-wrapper navbar-avatar-name">
            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
        </div>
    @endif

    <div class="position-absolute translate-middle bottom-0 mb-1 start-100 ms-n1 bg-success rounded-circle h-8px w-8px">
    </div>
</div>

            <!--end::User-->
        </div>
        <!--begin::User account menu-->
        <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px"
            data-kt-menu="true">
            <!--begin::Menu item-->
            <div class="menu-item px-3">
                <div class="menu-content d-flex align-items-center px-3">
                    <!--begin::Avatar-->
                    <div class="symbol symbol-50px me-5">

                        @if (Auth::user()->avatar)
                            <img class="sidebar-avatar-img"
                                src="{{ asset('storage/user/avatar/' . Auth::user()->avatar) }}"
                                alt="{{ Auth::user()->name }}" />
                        @else
                            <div
                                class="symbol-label fs-3 bg-light-primary text-primary sidebar-avatar-wrapper navbar-avatar-sidebar-name">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        @endif

                    </div>

                    <!--end::Avatar-->
                    <!--begin::Username-->
                    <div class="d-flex flex-column">
                        <div class="fw-bold d-flex align-items-center fs-5 navbar-sidebar-name">
                            {{ ucwords(strtolower(Auth::user()->name)) }}

                        </div>
                        <a class="fw-semibold text-muted text-hover-primary fs-7 navbar-sidebar-no_wa">
                            {{ ucwords(strtolower(Auth::user()->no_wa)) }}
                        </a>
                    </div>
                    <!--end::Username-->
                </div>
            </div>
            <!--end::Menu item-->
            <!--begin::Menu separator-->
            <div class="separator my-2"></div>
            <!--end::Menu separator-->
            <!--begin::Menu item-->
            <div class="menu-item px-5">
                <a href="{{ route('my-profile.index') }}" class="menu-link px-5"> My Profile </a>
            </div>
            <!--end::Menu item-->

            <!--begin::Menu item-->
            <div class="menu-item px-5 my-1">
                <a href="{{ route('my-security.index') }}" class="menu-link px-5">
                    Change Password
                </a>
            </div>
            <!--end::Menu item-->

            <!--begin::Menu separator-->
            <div class="separator my-2"></div>
            <!--end::Menu separator-->
            <!--begin::Menu item-->
            <div class="menu-item px-5" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
                data-kt-menu-placement="left-start" data-kt-menu-offset="-15px, 0">
                <a href="#" class="menu-link px-5">
                    <span class="menu-title position-relative">
                        Mode
                        <span class="ms-5 position-absolute translate-middle-y top-50 end-0">
                            <i class="ki-outline ki-night-day theme-light-show fs-2"></i>
                            <i class="ki-outline ki-moon theme-dark-show fs-2"></i>
                        </span>
                    </span>
                </a>
                <!--begin::Menu-->
                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-150px"
                    data-kt-menu="true" data-kt-element="theme-mode-menu">
                    <!--begin::Menu item-->
                    <div class="menu-item px-3 my-0">
                        <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="light">
                            <span class="menu-icon" data-kt-element="icon">
                                <i class="ki-outline ki-night-day fs-2"></i>
                            </span>
                            <span class="menu-title"> Light </span>
                        </a>
                    </div>
                    <!--end::Menu item-->
                    <!--begin::Menu item-->
                    <div class="menu-item px-3 my-0">
                        <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="dark">
                            <span class="menu-icon" data-kt-element="icon">
                                <i class="ki-outline ki-moon fs-2"></i>
                            </span>
                            <span class="menu-title"> Dark </span>
                        </a>
                    </div>
                    <!--end::Menu item-->
                    <!--begin::Menu item-->
                    <div class="menu-item px-3 my-0">
                        <a href="#" class="menu-link px-3 py-2" data-kt-element="mode" data-kt-value="system">
                            <span class="menu-icon" data-kt-element="icon">
                                <i class="ki-outline ki-screen fs-2"></i>
                            </span>
                            <span class="menu-title"> System </span>
                        </a>
                    </div>
                    <!--end::Menu item-->
                </div>
                <!--end::Menu-->
            </div>
            <!--end::Menu item-->


            <!--begin::Menu item-->

            <div class="menu-item px-5">
                <a href="#" class="menu-link px-5" id="btn-logout">
                    Sign Out
                </a>
            </div>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>

            <!--end::Menu item-->
        </div>
        <!--end::User account menu-->
        <!--end::Menu wrapper-->
    </div>
    <!--end::User menu-->
    <!--begin::Header menu toggle-->
    <div class="app-navbar-item d-lg-none ms-2 me-n3" title="Show header menu">
        <div class="btn btn-icon btn-color-gray-500 btn-active-color-primary w-35px h-35px"
            id="kt_app_sidebar_mobile_toggle">
            <i class="ki-outline ki-text-align-left fs-1"></i>
        </div>
    </div>
    <!--end::Header menu toggle-->
</div>
<script>
    document.getElementById('btn-logout').addEventListener('click', function(e) {
        e.preventDefault();

        Swal.fire({
            title: "Keluar dari Aplikasi?",
            html: `
            <div class="text-muted">Anda yakin ingin logout?</div>
        `,
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, Logout",
            cancelButtonText: "Batal",
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
        }).then((result) => {
            if (result.isConfirmed) {

                // === SweetAlert Loader Premium ===
                Swal.fire({
                    title: "<b>Memproses Logout...</b>",
                    html: `
                    <div style="display:flex; justify-content:center; gap:10px; margin-top:20px;">
                        <div class="dot-loader"></div>
                        <div class="dot-loader dot-loader--2"></div>
                        <div class="dot-loader dot-loader--3"></div>
                    </div>
                `,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    timer: 1500,

                }).then(() => {
                    document.getElementById('logout-form').submit();
                });

                // Tambahkan style loader
                const styleDots = document.createElement('style');
                styleDots.textContent = `
                .dot-loader {
                    width: 12px;
                    height: 12px;
                    background-color: #d33;
                    border-radius: 50%;
                    animation: bounceDot 0.6s infinite alternate;
                }
                .dot-loader--2 { animation-delay: 0.15s; }
                .dot-loader--3 { animation-delay: 0.3s; }

                @keyframes bounceDot {
                    0% { transform: translateY(0); opacity: 1; }
                    100% { transform: translateY(-10px); opacity: 0.4; }
                }
            `;
                document.head.appendChild(styleDots);
            }
        });
    });
</script>
