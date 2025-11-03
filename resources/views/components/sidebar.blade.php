<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <img alt="image" src="{{ asset('images/CSWDO.webp') }}" class="" style="width: 50px" />
            <a href="{{ route('admin.dashboard') }}">Burial Assistance</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <img alt="image" src="{{ asset('images/CSWDO.webp') }}" class="" style="width: 50px" />
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">Main</li>
            <li @class(['active' => Request::is('admin/dashboard')])>
                <a href="{{ route('admin.dashboard') }}" class="nav-link"><i class="fas fa-home"></i><span>Dashboard</span></a>
            </li>
            @can('view-reports')
                <li @class(['nav-item', 'dropdown', 'active' => Request::is('reports/*')])>
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                        <i class="fas fa-chart-line"></i>
                        <span>Reports</span>
                    </a>
                    <ul class="dropdown-menu">
                        <li @class(['active' => Request::is('reports/burial-assistances')])>
                            <a href="{{ route('reports.burial-assistances') }}" class="nav-link">
                                <span>Burial Assistances</span>
                            </a>
                        </li>
                        <li @class(['active' => Request::is('reports/deceased')])>
                            <a href="{{ route('reports.deceased') }}">
                                <span>Deceased</span>
                            </a>
                        </li>
                        <li @class(['active' => Request::is('reports/claimants')])>
                            <a href="{{ route('reports.claimants') }}">
                                <span>Claimants</span>
                            </a>
                        </li>
                        <li @class(['active' => Request::is('reports/cheques')])>
                            <a href="{{ route('reports.cheques') }}">
                                <span>Cheques</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

            <li class="menu-header">Applications</li>
            <li @class(['active' => Request::is('admin/applications/all')])>
                <a href="{{ route('admin.applications', ['status' => 'all']) }}" class="nav-link">
                    <i class="fas fa-file-lines"></i>
                    <span>All</span>
                </a>
            </li>
            <li @class(['nav-item', 'dropdown', 'active' => Request::is('admin/applications/pending', 'admin/applications/processing', 'admin/applications/approved')])>
            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                    <i class="fas fa-file-contract"></i>
                    <span>On-Going</span>
                </a>
                <ul class="dropdown-menu">
                    <li @class(['active' => Request::is('admin/applications/pending')])>
                        <a href="{{ route('admin.applications', ['status' => 'pending']) }}" class="nav-link">
                            <span>Pending</span>
                        </a>
                    </li>
                    <li @class(['active' => Request::is('admin/applications/processing')])>
                        <a href="{{ route('admin.applications', ['status' => 'processing']) }}" class="nav-link">
                            <span>Processing</span>
                        </a>
                    </li>
                    <li @class(['active' => Request::is('admin/applications/approved')])>
                        <a href="{{ route('admin.applications', ['status' => 'approved']) }}" class="nav-link">
                            <span>Approved</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li @class(['nav-item', 'dropdown', 'active' => Request::is('admin/applications/released', 'admin/applications/rejected')])>
            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                    <i class="fas fa-box-archive"></i>
                    <span>Closed</span>
                </a>
                <ul class="dropdown-menu">
                    <li @class(['active' => Request::is('admin/applications/released')])>
                        <a href="{{ route('admin.applications', ['status' => 'released']) }}" class="nav-link">
                            <span>Released</span>
                        </a>
                    </li>
                    <li @class(['active' => Request::is('admin/applications/rejected')])>
                        <a href="{{ route('admin.applications', ['status' => 'rejected']) }}" class="nav-link">
                            <span>Rejected</span>
                        </a>
                    </li>
                </ul>
            </li>

            <li class="menu-header">System</li>
            @can('view-logs')
                <li @class(['active' => Request::is('activity-logs')])>
                    <a href="{{ route('activity.logs') }}" class="nav-link"><i class="fas fa-clipboard-list"></i><span>Activity Logs</span></a>
                </li>
            @endcan
            @dd(auth()->user()->permissions())
            @can('manage-roles')
                <li @class(['active' => Request::is('superadmin/permissions')])>
                    <a href="{{ route('superadmin.permissions') }}" class="nav-link"><i class="fas fa-shield"></i><span>Permissions</span></a>
                </li>
                <li @class(['active' => Request::is('superadmin/roles')])>
                    <a href="{{ route('superadmin.roles') }}" class="nav-link"><i class="fas fa-id-badge"></i><span>Roles</span></a>
                </li>
            @endcan
        </ul>
    </aside>
</div>