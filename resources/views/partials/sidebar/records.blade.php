<!--begin:records-->
<div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="right-start"
    @class([
        'menu-item',
        'here' =>
            Route::is('interview*') ||
            Route::is('application*') ||
            Route::is('client*') ||
            Route::is('beneficiary*') ||
            Route::is('referral*'),
    ])>
    <!--begin:Menu link-->
    <span class="menu-link menu-center d-flex flex-column">
        <span class="menu-icon me-0">
            <x-ki-icon :icon_name="'folder'" :icon_size="'2x'" :paths_count="2" />
        </span>
        <small class="text-center text-gray-400 fw-semibold mt-1">Records</small>
    </span>
    <!--end:Menu link-->
    <!--begin:Menu sub-->
    <div class="menu-sub menu-sub-dropdown menu-sub-indentation px-2 py-4 w-250px mh-75 overflow-auto">
        <div class="menu-item">
            <div class="menu-content">
                <span class="menu-section fs-5 fw-bolder ps-1 py-1">Records</span>
            </div>
        </div>
        @include('components.sidebar-menu-link', [
            'route' => 'client.index',
            'activeLink' => 'client',
            'text' => 'Clients',
        ])
        @include('components.sidebar-menu-link', [
            'route' => 'beneficiary.index',
            'activeLink' => 'beneficiary',
            'text' => 'Beneficiaries',
        ])
        @include('components.sidebar-menu-link', [
            'route' => 'interview.index',
            'activeLink' => 'interview',
            'text' => 'Interviews',
        ])
        @include('components.sidebar-menu-link', [
            'route' => 'referral.index',
            'activeLink' => 'referral',
            'text' => 'Referrals',
        ])
        @include('components.sidebar-menu-link', [
            'route' => 'application.index',
            'activeLink' => 'application',
            'text' => 'Applications',
        ])
    </div>
    <!--end:Menu sub-->
</div>
<!--end:records-->
