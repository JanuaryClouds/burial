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
            <li class="menu-header">Applications</li>
            <li @class(['active' => Request::is('admin/applications/pending*')])>
                <a href="{{ route('admin.applications.pending') }}" class="nav-link"><i class="far fa-hourglass"></i><span>Pending</span"></a>
            </li>
            <li @class(['active' => Request::is('admin/applications/processing*')])>
                <a href="{{ route('admin.applications.processing') }}" class="nav-link"><i class="far fa-file-alt"></i><span>Processing</span"></a>
            </li>
            <li @class(['active' => Request::is('admin/applications/approved*')])>
                <a href="{{ route('admin.applications.approved') }}" class="nav-link"><i class="far fa-credit-card"></i><span>Approved (w/ Cheques)</span"></a>
            </li>
            <li @class(['active' => Request::is('admin/applications/released*')])>
                <a href="{{ route('admin.applications.released') }}" class="nav-link"><i class="far fa-circle-check"></i><span>Released</span"></a>
            </li>
            <li @class(['active' => Request::is('admin/applications/history*')])>
                <a href="" class="nav-link"><i class="fas fa-history"></i><span>History</span"></a>
            </li>
        </ul>
    </aside>
</div>