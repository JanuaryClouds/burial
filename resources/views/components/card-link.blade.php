@props([
    'route' => '#',
    'active' => true,
    'title',
    'description' => '',
    'icon',
    'icon_paths',
    'classes' => '',
])

<a href="{{ $active ? $route : '#' }}"
    class="card w-100 p-10 h-100 {{ $active && $route !== '#' ? 'hover-elevate-up parent-hover' : 'cursor-default' }} {{ $classes }} overflow-hidden position-relative">
    <div class="d-flex justify-content-between align-items-center">
        <span class="parent-hover-primary">
            <p class="fw-bold fs-1 {{ $active && $route !== '#' ? '' : 'text-muted' }}">{{ $title }}</p>
            <p class="text-muted mb-0">{{ $route !== '#' ? $description : 'Coming Soon' }}</p>
        </span>
    </div>
    <i class="{{ $icon }} position-absolute top-50 end-0 translate-middle-y opacity-25 {{ $active ? 'parent-hover-primary' : '' }}"
        style="z-index: 0; right: 10rem; font-size: 15rem; rotate: 15deg">
        @for ($index = 0; $index < $icon_paths; $index++)
            <span class="path{{ $index + 1 }}"></span>
        @endfor
    </i>
</a>
