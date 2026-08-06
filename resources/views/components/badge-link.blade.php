@props([
    'route' => '#',
    'badge_color' => 'info',
    'badge_size' => null,
])

<a href="{{ $route }}"
	class="badge {{ $badge_size ? 'badge-' . $badge_size : '' }} badge-pill bg-{{ $badge_color }}">
	{{ $slot }}
</a>
