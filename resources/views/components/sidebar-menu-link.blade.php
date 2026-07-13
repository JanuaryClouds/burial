@props([
    'route',
    'activeLink',
    'icon' => null,
    'iconPaths' => 0,
    'text',
])
<div class="menu-item">
    <a href="{{ route($route) }}" @class(['active' => Request::is($activeLink . '*'), 'menu-link'])>
        @if ($icon != null && $iconPaths != 0)
            <span class="menu-icon">
                <x-ki-icon :icon_name="'$icon'" :icon_size="'2'" :paths_count="$iconPaths" />
            </span>
        @else
            <span class="menu-bullet">
                <span class="bullet bullet-dot"></span>
            </span>
        @endif
        <span class="menu-title">{{ $text }}</span>
    </a>
</div>
