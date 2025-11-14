<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <img alt="image" src="{{ asset('images/CSWDO.webp') }}" class="" style="width: 50px" />
            <a href="{{ route('dashboard') }}">Burial Assistance</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <img alt="image" src="{{ asset('images/CSWDO.webp') }}" class="" style="width: 50px" />
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">Main</li>
            <li @class(['active' => Request::is('dashboard')])>
                <a href="{{ route('dashboard') }}" class="nav-link"><i class="fas fa-home"></i><span>Dashboard</span></a>
            </li>
            @can('manage-assignments')
                <li @class(['active' => Request::is('assignments')])>
                    <a href="{{ route('assignments') }}">
                        <i class="fas fa-check-to-slot"></i>
                        <span>Assignments</span>
                    </a>
                </li>
            @endcan
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

            <li class="menu-header">Clients</li>
            <li @class(['active' => Request::is('client*')])>
                <a href="{{ route('clients') }}" class="nav-link"><i class="fas fa-users"></i><span>Clients</span></a>
            </li>

            <li class="menu-header">Applications</li>
            <li @class(['active' => Request::is('funeral-assistances*')])>
                <a href="{{ route('funeral-assistances') }}" class="nav-link">
                    <i class="fas fa-file-lines"></i>
                    <span>Funeral Assistances</span>
                </a>
            </li>
            <li @class(['active' => Request::is('applications/all')])>
                <a href="{{ route('applications', ['status' => 'all']) }}" class="nav-link">
                    <i class="fas fa-file-lines"></i>
                    <span>All</span>
                </a>
            </li>
            <li @class(['nav-item', 'dropdown', 'active' => Request::is('applications/pending', 'applications/processing', 'applications/approved')])>
                <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                    <i class="fas fa-file-contract"></i>
                    <span>On-Going</span>
                </a>
                <ul class="dropdown-menu">
                    <li @class(['active' => Request::is('applications/pending')])>
                        <a href="{{ route('applications', ['status' => 'pending']) }}" class="nav-link">
                            <span>Pending</span>
                        </a>
                    </li>
                    <li @class(['active' => Request::is('applications/processing')])>
                        <a href="{{ route('applications', ['status' => 'processing']) }}" class="nav-link">
                            <span>Processing</span>
                        </a>
                    </li>
                    <li @class(['active' => Request::is('applications/approved')])>
                        <a href="{{ route('applications', ['status' => 'approved']) }}" class="nav-link">
                            <span>Approved</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li @class(['nav-item', 'dropdown', 'active' => Request::is('applications/released', 'applications/rejected')])>
            <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                    <i class="fas fa-box-archive"></i>
                    <span>Closed</span>
                </a>
                <ul class="dropdown-menu">
                    <li @class(['active' => Request::is('applications/released')])>
                        <a href="{{ route('applications', ['status' => 'released']) }}" class="nav-link">
                            <span>Released</span>
                        </a>
                    </li>
                    <li @class(['active' => Request::is('applications/rejected')])>
                        <a href="{{ route('applications', ['status' => 'rejected']) }}" class="nav-link">
                            <span>Rejected</span>
                        </a>
                    </li>
                </ul>
            </li>

            @can(['manage-content', 'manage-accounts'])
                <li class="menu-header">CMS</li>
                <li @class(['active' => Request::is('cms/barangays')])>
                    <a href="{{ route('cms.barangays') }}">
                        <i class="fas fa-city"></i>
                        <span>Barangays</span>
                    </a>
                </li>
                <li @class(['active' => Request::is('cms/relationship')])>
                    <a href="{{ route('cms.relationships') }}">
                        <i class="fas fa-people-roof"></i>
                        <span>Relationships</span>
                    </a>
                </li>
                <li @class(['active' => Request::is('cms/workflow')])>
                    <a href="{{ route('cms.workflow') }}">
                        <i class="fas fa-diagram-next"></i>
                        <span>Workflow Steps</span>
                    </a>
                </li>
                <li @class(['active' => Request::is('cms/handlers')])>
                    <a href="{{ route('cms.handlers') }}">
                        <i class="fas fa-clipboard-user"></i>
                        <span>Handlers</span>
                    </a>
                </li>
                <li @class(['active' => Request::is('cms/users') || Request::is('users/*')])>
                    <a href="{{ route('cms.users') }}">
                        <i class="fas fa-users"></i>
                        <span>Users</span>
                    </a>
                </li>
                <li @class(['active' => Request::is('cms/religions')])>
                    <a href="{{ route('cms.religions') }}">
                        <i class="fas fa-church"></i>
                        <span>Religions</span>
                    </a>
                </li>
            @endcan

            @if (auth()->user()->can('view-logs') || auth()->user()->can('manage-roles'))
                <li class="menu-header">System</li>
            @endif
            @can('view-logs')
                <li @class(['active' => Request::is('activity-logs')])>
                    <a href="{{ route('activity.logs') }}" class="nav-link"><i class="fas fa-clipboard-list"></i><span>Activity Logs</span></a>
                </li>
            @endcan
            @can('manage-roles')
                <li @class(['active' => Request::is('permissions')])>
                    <a href="{{ route('permissions') }}" class="nav-link"><i class="fas fa-shield"></i><span>Permissions</span></a>
                </li>
                <li @class(['active' => Request::is('roles')])>
                    <a href="{{ route('roles') }}" class="nav-link"><i class="fas fa-id-badge"></i><span>Roles</span></a>
                </li>
            @endcan
        </ul>
    </aside>
</div>