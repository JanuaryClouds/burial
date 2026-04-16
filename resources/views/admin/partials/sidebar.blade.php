<!-- begin::Aside -->
<div class="aside" data-kt-drawer="true" data-kt-drawer-name="aside" data-kt-drawer-activate="{default: true, lg: false}"
    data-kt-drawer-overlay="true" data-kt-drawer-width="auto" data-kt-drawer-direction="start"
    data-kt-drawer-toggle="#kt_aside_toggle">
    <!--begin::Logo-->
    <div class="aside-logo flex-column-auto pt-10 pt-lg-7" id="kt_aside_logo">
        <a href="{{ route('dashboard') }}">
            <img alt="Logo" src="{{ asset('images/CSWDO.webp') }}" class="h-60px bg-white rounded-circle">
        </a>
    </div>
    <!--end::Logo-->

    <!--begin::Nav-->
    <div class="aside-menu flex-column-fluid pt-0 pb-7 py-lg-10" id="kt_aside_menu">
        <!--begin::Aside menu-->
        <div class="w-100 hover-scroll-y scroll-lg-ms d-flex ps-lg-3" id="kt_aside_menu_wrapper" data-kt-scroll="true"
            data-kt-scroll-activate="{default: false, lg: true}" data-kt-scroll-height="auto"
            data-kt-scroll-dependencies="#kt_aside_logo, #kt_aside_footer"
            data-kt-scroll-wrappers="#kt_aside, #kt_aside_menu_wrapper" data-kt-scroll-offset="0">
            <div id="kt_aside_menu"
                class="menu menu-column menu-title-gray-600 menu-state-primary menu-state-icon-primary menu-state-bullet-primary menu-icon-gray-500 menu-arrow-gray-500 fw-semibold fs-6 my-auto"
                data-kt-menu="true">
                @include('admin.partials.sidebar-common')
                @if (auth()->user()->roles()->count() == 0)
                    @include('client.partials.sidebar')
                @endif
                @can('view-clients')
                    @include('admin.partials.sidebar-records')
                @endcan
                @can('manage-content')
                    @include('superadmin.partials.sidebar-cms')
                @endcan
                @can('view-reports')
                    @include('admin.partials.sidebar-reports')
                @endcan
                @can('view-logs')
                    @include('admin.partials.sidebar-logs')
                @endcan
                @if (auth()->user()->canAny(['view-roles', 'view-users', 'edit-system-settings']))
                    @include('admin.partials.sidebar-system')
                @endif
            </div>
        </div>
    </div>
    <!--end::Nav-->
</div>
<!--end::Aside menu-->
