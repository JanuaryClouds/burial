<!-- begin:Reports -->
<div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item py-2">
    <span class="menu-link menu-center d-flex flex-column">
        <span class="menu-icon me-0">
            <i class="ki-duotone ki-file-sheet fs-2x">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
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
        <!-- TODO: Update Routes to be dynamic for reports -->
        <!-- TODO: Include clients and funeral assistances -->
        <div class="menu-item">
            <a href="{{ route('reports.burial-assistances') }}" @class(['active' => Request::is('reports/burial-assistances'), 'menu-link'])>
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title">Burial Assistances</span>
            </a>
        </div>
        <div class="menu-item">
            <a href="{{ route('reports.deceased') }}" @class(['active' => Request::is('reports/deceased'), 'menu-link'])>
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title">Deceased</span>
            </a>
        </div>
        <div class="menu-item">
            <a href="{{ route('reports.claimants') }}" @class(['active' => Request::is('reports/claimants'), 'menu-link'])>
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title">Claimants</span>
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