<div id="main-header-web" class="header py-6 py-lg-0" data-kt-sticky="true" data-kt-sticky-name="header"
    data-kt-sticky-offset="{lg: '300px'}"
    style="background: url('{{ asset('images/banner-light.svg') }}') no-repeat center center / cover; background-color: #071437;">
    <!--begin::Container-->
    <div class="header-container container-xxl">
        <!--begin::Page title-->
        <div
            class="page-title d-flex flex-column align-items-start justify-content-center flex-wrap me-lg-20 py-3 py-lg-0 me-3">
            <!--begin::Heading-->
            <h1 class="d-flex flex-column text-gray-900 fw-bold my-1">
                <span class="text-white fs-1">{{ $page_title ?? 'CSWDO - Funeral Assistance' }}</span>
                <small class="text-gray-600 fs-6 fw-normal pt-2">
                    {{ $page_subtitle ?? 'Today is ' . \Carbon\Carbon::now()->format('l, F d, Y') }}.
                </small>
            </h1>
            <!--end::Heading-->
        </div>
        <!--end::Page title--->
        <!--begin::Wrapper-->
        <div class="d-flex align-items-center flex-wrap gap-3">
            <!-- begin::Menu wrapper -->
            @includeWhen(Route::is('funeral.show'), 'funeral.partials.action-buttons')
            @includeWhen(Route::is('client.show'), 'client.partial.action-buttons')
            @includeWhen(Route::is('*.index') && auth()->user()->can('manage-content'),
                'superadmin.partial.new-content')
            <!-- end::Menu wrapper -->

            <!-- begin::Theme mode -->
            @include('components.theme-toggle')
            <!-- end::Theme mode -->
            <div class="">
                <!-- begin::User -->
                <a href="#" class="btn btn-icon btn-custom btn-active-color-primary menu-dropdown"
                    data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
                    <i class="ki-duotone ki-user fs-1"><span class="path1"></span><span class="path2"></span></i>
                </a>

                <!--begin::User account menu-->
                <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-gray-800 menu-state-bg menu-state-color fw-semibold py-4 fs-6 w-275px"
                    data-kt-menu="true" data-popper-placement="bottom-end"
                    style="z-index: 107; position: fixed; inset: 0px 0px auto auto; margin: 0px; transform: translate3d(-230.4px, 107.2px, 0px);">
                    <!--begin::Menu item-->
                    <div class="menu-item px-3">
                        <div class="menu-content d-flex align-items-center px-3">
                            <!--begin::Avatar-->
                            <div class="symbol symbol-50px me-5">
                                <img alt="Logo" src="{{ asset('images/avatar/avatar-1.png') }}">
                            </div>
                            <!--end::Avatar-->
                            <!--begin::Username-->
                            <div class="d-flex flex-column">
                                <div class="fw-bold d-flex align-items-center fs-5">
                                    {{ auth()->user()->first_name . ' ' . auth()->user()->last_name }}
                                </div>

                                <a href="#" class="fw-semibold text-muted text-hover-primary fs-7">
                                    {{ auth()->user()->email }}
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
                        <form action="{{ route('logout') }}" method="POST" class="block mb-0">
                            @csrf
                            <button type="submit" class="btn w-100 text-left">
                                <span class="fw-medium">
                                    <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                                </span>
                            </button>
                        </form>
                    </div>
                    <!--end::Menu item-->
                </div>
                <!--end::User account menu-->
                <!-- end::User -->
            </div>
        </div>
        <!--end::Wrapper-->
    </div>
    <!--end::Container-->
    <div class="header-offset"></div>
</div>

<script>
    const header = document.getElementById('main-header-web')
    const darkBanner = "{{ asset('images/banner-dark.svg') }}";
    const lightBanner = "{{ asset('images/banner-light.svg') }}";

    document.querySelectorAll('[data-kt-element="mode"]').forEach(mode => {
        mode.addEventListener('click', () => {
            if (mode.getAttribute('data-kt-value') === 'light') {
                header.style.background = `url('${lightBanner}') no-repeat center center / cover`;
                header.style.backgroundColor = '#071437';
            } else if (mode.getAttribute('data-kt-value') === 'dark') {
                header.style.background = `url('${darkBanner}') no-repeat center center / cover`;
                header.style.backgroundColor = '#071437';
            } else if (mode.getAttribute('data-kt-value') === 'system') {
                if (window.matchMedia('(prefers-color-scheme: light)').matches) {
                    header.style.background = `url('${lightBanner}') no-repeat center center / cover`;
                    header.style.backgroundColor = '#071437';
                } else {
                    header.style.background = `url('${darkBanner}') no-repeat center center / cover`;
                    header.style.backgroundColor = '#071437';
                }
            } else {
                header.style.background = `url('${lightBanner}') no-repeat center center / cover`;
                header.style.backgroundColor = '#071437';
            }
        })
    })
</script>
