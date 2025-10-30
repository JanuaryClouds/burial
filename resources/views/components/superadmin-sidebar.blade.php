<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <img alt="image" src="{{ asset('images/CSWDO.webp') }}" class="" style="width: 50px" />
            <a href="{{ route('superadmin.dashboard') }}">Burial Assistance</a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <img alt="image" src="{{ asset('images/CSWDO.webp') }}" class="" style="width: 50px" />
        </div>
        <ul class="sidebar-menu">
            <li class="menu-header">Main</li>
            <li @class(['active' => Request::is('superadmin/dashboard')])>
                <a href="{{ route('superadmin.dashboard') }}">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            <li @class(['active' => Request::is('superadmin/assignments')])>
                <a href="{{ route('superadmin.assignments') }}">
                    <i class="fas fa-check-to-slot"></i>
                    <span>Assignments</span>
                </a>
            </li>

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

            <li class="menu-header">CMS</li>
            <li @class(['active' => Request::is('superadmin/cms/barangays')])>
                <a href="{{ route('superadmin.cms.barangays') }}">
                    <i class="fas fa-city"></i>
                    <span>Barangays</span>
                </a>
            </li>
            <li @class(['active' => Request::is('superadmin/cms/relationships')])>
                <a href="{{ route('superadmin.cms.relationships') }}">
                    <i class="fas fa-people-roof"></i>
                    <span>Relationships</span>
                </a>
            </li>
            <li @class(['active' => Request::is('superadmin/cms/workflow')])>
                <a href="{{ route('superadmin.cms.workflow') }}">
                    <i class="fas fa-diagram-next"></i>
                    <span>Workflow Steps</span>
                </a>
            </li>
            <li @class(['active' => Request::is('superadmin/cms/handlers')])>
                <a href="{{ route('superadmin.cms.handlers') }}">
                    <i class="fas fa-clipboard-user"></i>
                    <span>Handlers</span>
                </a>
            </li>
            <li @class(['active' => Request::is('superadmin/cms/users') || Request::is('superadmin/users/*')])>
                <a href="{{ route('superadmin.cms.users') }}">
                    <i class="fas fa-users"></i>
                    <span>Users</span>
                </a>
            </li>
            <li @class(['active' => Request::is('superadmin/cms/religions')])>
                <a href="{{ route('superadmin.cms.religions') }}">
                    <i class="fas fa-church"></i>
                    <span>Religions</span>
                </a>
            </li>

            <li class="menu-header">Logs and Activity</li>
            <li @class(['active' => Request::is('activity-logs')])>
                <a href="{{ route('activity.logs') }}" class="nav-link"><i class="fas fa-clipboard-list"></i><span>Activity Logs</span></a>
            </li>
        </ul>
    </aside>
</div>