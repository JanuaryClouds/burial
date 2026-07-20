@props([
    'type' => 'button',
    'class' => '',
    'toggle' => 'tooltip',
    'placement' => 'top',
    'title' => '',
])

<button type="{{ $type }}" class="btn {{ $class }}" data-bs-toggle="{{ $toggle }}"
    data-bs-placement="{{ $placement }}" title="{{ $title }}">
    {{ $slot }}
</button>
