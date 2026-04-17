<!--begin:client-->
<div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="right-start"
    @class([
        'menu-item',
        'here' =>
            Route::is('burial*') ||
            Route::is('assignments*') ||
            Route::is('funeral*') ||
            Route::is('client*') ||
            Route::is('beneficiary*'),
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
        @can('view-clients')
            <div class="menu-item">
                <a href="{{ route('client.index') }}" @class(['active' => Request::is('client*'), 'menu-link'])>
                    <span class="menu-icon">
                        <x-ki-icon :icon_name="'people'" :icon_size="'2'" :paths_count="5" />
                    </span>
                    <span class="menu-title">Clients</span>
                </a>
            </div>
        @endcan
        @can('view-clients')
            <div class="menu-item">
                <a href="{{ route('beneficiary.index') }}" @class(['active' => Request::is('beneficiary*'), 'menu-link'])>
                    <span class="menu-icon">
                        <x-ki-icon :icon_name="'people'" :icon_size="'2'" :paths_count="5" />
                    </span>
                    <span class="menu-title">Beneficiaries</span>
                </a>
            </div>
        @endcan
        <div class="menu-item">
            <a href="{{ route('funeral.index') }}" @class(['active' => Request::is('funeral*'), 'menu-link'])>
                <span class="menu-icon">
                    <x-ki-icon :icon_name="'file-up'" :icon_size="'2'" :paths_count="2" />
                </span>
                <span class="menu-title">Libreng Libing</span>
            </a>
        </div>
        <div class="menu-item">
            <a href="{{ route('burial.index') }}" @class(['active' => Request::is('burial*'), 'menu-link'])>
                <span class="menu-icon">
                    <x-ki-icon :icon_name="'file-up'" :icon_size="'2'" :paths_count="2" />
                </span>
                <span class="menu-title">Burial Assistance</span>
            </a>
        </div>
    </div>
    <!--end:Menu sub-->
</div>
<!--end:client-->
