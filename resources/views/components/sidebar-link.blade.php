@props([
    'route' => null,
    'active_link' => $route ?? null,
    'icon' => null,
    'icon_paths' => 0,
    'text' => null,
    'active' => true,
])

<a href="{{ $active ? $route ?? '#' : '#' }}" data-kt-menu-trigger="{default: 'click', lg: 'hover'}"
    data-kt-menu-placement="right-start" @class([
        'menu-item',
        'menu-item-active' => $active,
        'here' => $active_link ? Route::is($active_link) : false,
    ])>
    <span class="menu-link menu-center d-flex flex-column">
        <span class="menu-icon me-0">
            <x-ki-icon :icon_name="$icon" :icon_size="'2x'" :paths_count="$icon_paths" />
        </span>
        <small class="text-center text-gray-400 fw-semibold mt-1">{{ $text }}</small>
    </span>
    <!--end:Menu link-->
    <!--begin:Menu sub-->
    <div class="menu-sub menu-sub-dropdown px-2 py-4 w-250px mh-75 overflow-auto">
        <div class="menu-item">
            <div class="menu-content">
                <span class="menu-section fs-5 fw-bolder ps-1 py-1">{{ $text }}</span>
            </div>
        </div>
    </div>
    <!--end:Menu sub-->
</a>
<!--end:Common Pages-->
