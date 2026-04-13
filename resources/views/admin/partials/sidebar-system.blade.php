<div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="right-start"
    @class([
        'menu-item',
        'here' => Route::is('role*') || Route::is('user*') || Route::is('system.*'),
    ])>
    <span class="menu-link menu-center d-flex flex-column">
        <span class="menu-icon me-0">
            <x-ki-icon :icon_name="'setting-2'" :icon_size="'2x'" :paths_count="2" />
        </span>
        <small class="text-center text-gray-400 fw-semibold mt-1">System</small>
    </span>
    <div class="menu-sub menu-sub-dropdown px-2 py-4 w-250px mh-75 overflow-auto">
        <div class="menu-item">
            <div class="menu-content ">
                <span class="menu-section fs-5 fw-bolder ps-1 py-1">
                    System
                </span>
            </div>
        </div>
        @can('view-users')
            <div class="menu-item">
                <a href="{{ route('user.index') }}" @class(['active' => Route::is('user.*'), 'menu-link'])>
                    <span class="menu-bullet">
                        <span class="bullet bullet-dot"></span>
                    </span>
                    <span class="menu-title">Users</span>
                </a>
            </div>
        @endcan
        @can('view-roles')
            <div class="menu-item">
                <a href="{{ route('role.index') }}" @class(['active' => Route::is('role.*'), 'menu-link'])>
                    <span class="menu-bullet">
                        <span class="bullet bullet-dot"></span>
                    </span>
                    <span class="menu-title">Roles</span>
                </a>
            </div>
        @endcan
        @can('edit-system-settings')
            <div class="menu-item">
                <a href="{{ route('system.index') }}" @class(['active' => Route::is('system.*'), 'menu-link'])>
                    <span class="menu-bullet">
                        <span class="bullet bullet-dot"></span>
                    </span>
                    <span class="menu-title">Settings</span>
                </a>
            </div>
        @endcan
    </div>
</div>
