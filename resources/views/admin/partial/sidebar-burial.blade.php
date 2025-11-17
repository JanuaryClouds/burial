<!--begin:client-->
<div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item py-2">
    <!--begin:Menu link-->
    <span class="menu-link menu-center d-flex flex-column">
        <span class="menu-icon me-0">
            <i class="ki-duotone ki-document fs-2x">
                <span class="path1"></span>
                <span class="path2"></span>
            </i>
        </span>
        <small class="text-center text-gray-400 fw-semibold mt-1">Burial</small>
    </span>
    <!--end:Menu link-->
    <!--begin:Menu sub-->
    <div class="menu-sub menu-sub-dropdown menu-sub-indentation px-2 py-4 w-250px mh-75 overflow-auto">
        <div class="menu-item">
            <div class="menu-content">
                <span class="menu-section fs-5 fw-bolder ps-1 py-1">
                    On-Going
                </span>
            </div>
        </div>
        <div class="menu-item">
            <a href="{{ route('applications', ['status' => 'pending']) }}" @class(['active' => Request::is('applications/pending'), 'menu-link'])>
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title">Pending</span>
            </a>
        </div>
        <div class="menu-item">
            <a href="{{ route('applications', ['status' => 'processing']) }}" @class(['active' => Request::is('applications/processing'), 'menu-link'])>
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title">Proccessing</span>
            </a>
        </div>
        <div class="menu-item">
            <a href="{{ route('applications', ['status' => 'approved']) }}" @class(['active' => Request::is('applications/approved'), 'menu-link'])>
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title">Approved</span>
            </a>
        </div>
        <div class="menu-item">
            <div class="menu-content">
                <span class="menu-section fs-5 fw-bolder ps-1 py-1">
                    Completed
                </span>
            </div>
        </div>
        <div class="menu-item">
            <a href="{{ route('applications', ['status' => 'released']) }}" @class(['active' => Request::is('applications/released'), 'menu-link'])>
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title">Released</span>
            </a>
        </div>
        <div class="menu-item">
            <a href="{{ route('applications', ['status' => 'rejected']) }}" @class(['active' => Request::is('applications/rejected'), 'menu-link'])>
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title">Rejected</span>
            </a>
        </div>
    </div>
    <!--end:Menu sub-->
</div>
<!--end:client-->