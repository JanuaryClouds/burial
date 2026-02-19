<div class="d-flex d-lg-none align-items-center ms-3">
    <a href="#" class="btn btn-icon btn-custom btn-active-color-primary" data-kt-menu-trigger="{default:'click'}"
        data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end">
        <i class="ki-duotone ki-burger-menu fs-1">
            <span class="path1"></span>
            <span class="path2"></span>
            <span class="path3"></span>
            <span class="path4"></span>
        </i>
    </a>

    <!--begin::Menu-->
    <div class="menu menu-sub menu-sub-dropdown menu-column menu-rounded menu-title-gray-700 menu-icon-gray-500 menu-active-bg menu-state-color fw-semibold py-4 fs-base w-100 overflow-y-scroll mt-20"
        data-kt-menu="true" style="">
        <div class="menu-item px-3 my-0">
            <a href="{{ route('dashboard') }}" class="menu-link px-3 py-2 active">
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title">
                    Dashboard
                </span>
            </a>
        </div>
        <div class="menu-item">
            <a href="{{ route('client.index') }}" @class(['active' => Request::is('client*'), 'menu-link'])>
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title">Clients</span>
            </a>
        </div>
        <div class="menu-item">
            <a href="{{ route('funeral.index') }}" @class(['active' => Request::is('funeral*'), 'menu-link'])>
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title">Libreng Libing</span>
            </a>
        </div>
        <div class="menu-item">
            <a href="{{ route('burial.index', ['status' => 'all']) }}" @class([
                'active' => Request::is('burial.all'),
                'menu-link',
            ])>
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title">Burial Assistances</span>
            </a>
        </div>
        @can('manage-content')
            <div class="menu-item">
                <div class="menu-content">
                    <span class="menu-section fs-5 fw-bolder ps-1 py-1">
                        Content Management System
                    </span>
                </div>
            </div>
            <div class="menu-item">
                <a href="{{ route('barangay.index') }}" @class(['active' => Route::is('barangay.*'), 'menu-link'])>
                    <span class="menu-bullet">
                        <span class="bullet bullet-dot"></span>
                    </span>
                    <span class="menu-title">Barangays</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="{{ route('relationship.index') }}" @class(['active' => Route::is('relationship.*'), 'menu-link'])>
                    <span class="menu-bullet">
                        <span class="bullet bullet-dot"></span>
                    </span>
                    <span class="menu-title">Relationships</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="{{ route('workflowstep.index') }}" @class(['active' => Route::is('workflowstep.*'), 'menu-link'])>
                    <span class="menu-bullet">
                        <span class="bullet bullet-dot"></span>
                    </span>
                    <span class="menu-title">Workflow Steps</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="{{ route('handler.index') }}" @class(['active' => Route::is('handler.*'), 'menu-link'])>
                    <span class="menu-bullet">
                        <span class="bullet bullet-dot"></span>
                    </span>
                    <span class="menu-title">Handlers</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="{{ route('religion.index') }}" @class(['active' => Route::is('religion.*'), 'menu-link'])>
                    <span class="menu-bullet">
                        <span class="bullet bullet-dot"></span>
                    </span>
                    <span class="menu-title">Religions</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="{{ route('education.index') }}" @class(['active' => Route::is('education.*'), 'menu-link'])>
                    <span class="menu-bullet">
                        <span class="bullet bullet-dot"></span>
                    </span>
                    <span class="menu-title">Education</span>
                </a>
            </div>
            <div class="menu-item">
                <a href="{{ route('nationality.index') }}" @class(['active' => Route::is('nationality.*'), 'menu-link'])>
                    <span class="menu-bullet">
                        <span class="bullet bullet-dot"></span>
                    </span>
                    <span class="menu-title">Nationality</span>
                </a>
            </div>
        @endcan
        @can('view-reports')
            <div class="menu-item">
                <div class="menu-content">
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
        @endcan
        @can('view-logs')
            <div class="menu-item">
                <div class="menu-content">
                    <span class="menu-section fs-5 fw-bolder ps-1 py-1">
                        System
                    </span>
                </div>
            </div>
            <div class="menu-item">
                <a href="{{ route('activity.logs') }}" @class(['active' => Route::is('user.*'), 'menu-link'])>
                    <span class="menu-bullet">
                        <span class="bullet bullet-dot"></span>
                    </span>
                    <span class="menu-title">Logs</span>
                </a>
            </div>
        @endcan
        @can('manage-roles')
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
        @endcan
    </div>
    <!--end::Menu-->

</div>
