<!--begin:logs-->
<div id="sidebarLogsLink" data-kt-menu-trigger="{default: 'click', lg: 'hover'}" data-kt-menu-placement="right-start" class="menu-item py-2">
    <!--begin:Menu link-->
    <span class="menu-link menu-center d-flex flex-column">
        <span class="menu-icon me-0">
            <i class="ki-duotone ki-scroll fs-2x">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
            </i>
        </span>
        <small class="text-center text-gray-400 fw-semibold mt-1">Logs</small>
    </span>
    <!--end:Menu link-->
    <!--begin:Menu sub-->
    <div class="menu-sub menu-sub-dropdown px-2 py-4 w-250px mh-75 overflow-auto">
        <div class="menu-item">
            <div class="menu-content ">
                <span class="menu-section fs-5 fw-bolder ps-1 py-1">Logs</span>
            </div>
        </div>
    </div>
    <!--end:Menu sub-->
</div>
<!--end:logs-->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebarLogsLink = document.getElementById('sidebarLogsLink');
        sidebarLogsLink.addEventListener('click', function() {
            window.location.href = '{{ route('activity.logs') }}';
        });
    });
</script>