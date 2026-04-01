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
        <div class="menu-item">
            <a href="{{ route('client.index') }}" @class(['active' => Request::is('client*'), 'menu-link'])>
                <span class="menu-icon">
                    <x-ki-icon :icon_name="'people'" :icon_size="'2'" :paths_count="5" />
                </span>
                <span class="menu-title">Clients</span>
            </a>
        </div>
        <div class="menu-item">
            <a href="{{ route('beneficiary.index') }}" @class(['active' => Request::is('beneficiary*'), 'menu-link'])>
                <span class="menu-icon">
                    <x-ki-icon :icon_name="'people'" :icon_size="'2'" :paths_count="5" />
                </span>
                <span class="menu-title">Beneficiaries</span>
            </a>
        </div>
        <div class="menu-item">
            <a href="{{ route('funeral.index') }}" @class(['active' => Request::is('funeral*'), 'menu-link'])>
                <span class="menu-icon">
                    <x-ki-icon :icon_name="'file-up'" :icon_size="'2'" :paths_count="2" />
                </span>
                <span class="menu-title">Libreng Libing</span>
            </a>
        </div>
        <div data-kt-menu-trigger="click" class="menu-item menu-accordion">
            <span class="menu-link">
                <span class="menu-icon">
                    <x-ki-icon :icon_name="'file-up'" :icon_size="'2'" :paths_count="2" />
                </span>
                <span class="menu-title">
                    Burial Assistances
                </span>
                <span class="menu-arrow"></span>
            </span>
            <div class="menu-sub menu-sub-accordion" kt-hidden-height="334" style="">
                <div class="menu-item">
                    <a href="{{ route('burial.index', ['status' => 'all']) }}" @class([
                        'active' =>
                            request('status') === 'all' ||
                            (Request::is('burial.all') && !request('status')),
                        'menu-link',
                    ])>
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">All</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a href="{{ route('burial.index', ['status' => 'pending']) }}" @class([
                        'active' => Request::is('burial.pending'),
                        'menu-link',
                    ])>
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Pending</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a href="{{ route('burial.index', ['status' => 'processing']) }}" @class([
                        'active' => Request::is('burial.processing'),
                        'menu-link',
                    ])>
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Processing</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a href="{{ route('burial.index', ['status' => 'approved']) }}" @class([
                        'active' => Request::is('burial.approved'),
                        'menu-link',
                    ])>
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Approved</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a href="{{ route('burial.index', ['status' => 'released']) }}" @class([
                        'active' => Request::is('burial.released'),
                        'menu-link',
                    ])>
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Released</span>
                    </a>
                </div>
                <div class="menu-item">
                    <a href="{{ route('burial.index', ['status' => 'rejected']) }}" @class([
                        'active' => Request::is('burial.rejected'),
                        'menu-link',
                    ])>
                        <span class="menu-bullet">
                            <span class="bullet bullet-dot"></span>
                        </span>
                        <span class="menu-title">Rejected</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <!--end:Menu sub-->
</div>
<!--end:client-->
