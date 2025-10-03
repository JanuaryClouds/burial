<div class="main-sidebar sidebar-style-2">
    <aside class="sidebar-wrapper">
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
            <li class="menu-header">Reports</li>
            <li @class(['active' => Request::is('reports/burial-assistances')])>
                <a href="{{ route('reports.burial-assistances') }}">
                    <i class="fas fa-file"></i>
                    <span>Burial Assistances</span>
                </a>
            </li>
            <li @class(['active' => Request::is('reports/deceased')])>
                <a href="{{ route('reports.deceased') }}">
                    <i class="fas fa-image-portrait"></i>
                    <span>Deceased</span>
                </a>
            </li>
            <li @class(['active' => Request::is('reports/claimants')])>
                <a href="{{ route('reports.claimants') }}">
                    <i class="fas fa-user"></i>
                    <span>Claimants</span>
                </a>
            </li>
            <!-- TODO: cheques -->

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
            <li @class(['active' => Request::is('superadmin/cms/users')])>
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
        </ul>
    </aside>
</div>