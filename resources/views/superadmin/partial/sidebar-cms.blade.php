<!--begin:content-->
<div data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="right-start"
    @class([
        'menu-item',
        'here' => Route::is('cms*') || Route::is('cms*'),  
    ])
>
    <!--begin:Menu link-->
    <span class="menu-link menu-center d-flex flex-column">
        <span class="menu-icon me-0">
            <i class="ki-duotone ki-data fs-2x">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
                <span class="path4"></span>
                <span class="path5"></span>
            </i>
        </span>
        <small class="text-center text-gray-400 fw-semibold mt-1">CMS</small>
    </span>
    <!--end:Menu link-->
    <!--begin:Menu sub-->
    <div class="menu-sub menu-sub-dropdown menu-sub-indentation px-2 py-4 w-250px mh-75 overflow-auto">
        <div class="menu-item">
            <div class="menu-content">
                <span class="menu-section fs-5 fw-bolder ps-1 py-1">
                    Content Management System
                </span>
            </div>
        </div>
        <div class="menu-item">
            <a href="{{ route('cms.barangays') }}" @class(['active' => Request::is('cms/barangays'), 'menu-link'])>
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title">Barangays</span>
            </a>
        </div>
        <div class="menu-item">
            <a href="{{ route('cms.relationships') }}" @class(['active' => Request::is('cms/relationships'), 'menu-link'])>
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title">Relationships</span>
            </a>
        </div>
        <div class="menu-item">
            <a href="{{ route('cms.workflow') }}" @class(['active' => Request::is('cms/workflow'), 'menu-link'])>
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title">Workflow Steps</span>
            </a>
        </div>
        <div class="menu-item">
            <a href="{{ route('cms.handlers') }}" @class(['active' => Request::is('cms/handlers'), 'menu-link'])>
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title">Handlers</span>
            </a>
        </div>
        <div class="menu-item">
            <a href="{{ route('cms.religions') }}" @class(['active' => Request::is('cms/religions'), 'menu-link'])>
                <span class="menu-bullet">
                    <span class="bullet bullet-dot"></span>
                </span>
                <span class="menu-title">Religions</span>
            </a>
        </div>
    </div>
    <!--end:Menu sub-->
</div>
<!--end:content-->