<!-- begin:Reports -->
<div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="right-start"
    @class(['menu-item', 'here' => Route::is('reports*')])>
    <span class="menu-link menu-center d-flex flex-column">
        <span class="menu-icon me-0">
            <x-ki-icon :icon_name="'file-sheet'" :icon_size="'2x'" :paths_count="2" />
        </span>
        <small class="text-center text-gray-400 fw-semibold mt-1">Reports</small>
    </span>
    <div class="menu-sub menu-sub-dropdown px-2 py-4 w-250px mh-75 overflow-auto">
        <div class="menu-item">
            <div class="menu-content ">
                <span class="menu-section fs-5 fw-bolder ps-1 py-1">
                    Reports
                </span>
            </div>
        </div>
        <div class="menu-item">
            <a href="{{ route('reports.clients') }}" @class(['active' => Request::is('reports/clients'), 'menu-link'])>
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title">Clients</span>
            </a>
        </div>
        <div class="menu-item">
            <a href="{{ route('reports.beneficiaries') }}" @class([
                'active' => Request::is('reports/beneficiaries'),
                'menu-link',
            ])>
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title">Beneficiaries</span>
            </a>
        </div>
        <div class="menu-item">
            <a href="{{ route('reports.funerals') }}" @class(['active' => Request::is('reports/funerals'), 'menu-link'])>
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title">Libreng Libing</span>
            </a>
        </div>
        <div class="menu-item">
            <a href="{{ route('reports.burial-assistances') }}" @class([
                'active' => Request::is('reports/burial-assistances'),
                'menu-link',
            ])>
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title">Burial Assistances</span>
            </a>
        </div>
        <div class="menu-item">
            <a href="{{ route('reports.cheques') }}" @class(['active' => Request::is('reports/cheques'), 'menu-link'])>
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title">Checks</span>
            </a>
        </div>
    </div>
</div>
<!-- end:Reports -->
