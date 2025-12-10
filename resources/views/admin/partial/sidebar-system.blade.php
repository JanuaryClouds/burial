<div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="right-start"
    @class([
        'menu-item',
        'here' =>
            Route::is('permission*') || Route::is('role*') || Route::is('user*'),
    ])>
    <span class="menu-link menu-center d-flex flex-column">
        <span class="menu-icon me-0">
            <i class="ki-duotone ki-setting-2 fs-2x">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
        </span>
        <small class="text-center text-gray-400 fw-semibold mt-1">System</small>
    </span>
    <div class="menu-sub menu-sub-dropdown px-2 py-4 w-250px mh-75 overflow-auto">
        <div class="menu-item">
            <div class="menu-content ">
                <span class="menu-section fs-5 fw-bolder ps-1 py-1">
                    System Settings
                </span>
            </div>
        </div>
        <div class="menu-item">
            <a href="{{ route('user.index') }}" @class(['active' => Route::is('user.*'), 'menu-link'])>
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title">Users</span>
            </a>
        </div>
        <div class="menu-item">
            <a href="{{ route('permission.index') }}" @class(['active' => Request::is('permission.index'), 'menu-link'])>
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title">Permissions</span>
            </a>
        </div>
        <div class="menu-item">
            <a href="{{ route('role.index') }}" @class(['active' => Request::is('role.index'), 'menu-link'])>
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title">Roles</span>
            </a>
        </div>
    </div>
</div>
