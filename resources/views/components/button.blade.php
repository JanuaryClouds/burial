@props([
    'toggle' => 'tooltip',
    'placement' => 'top',
    'title' => '',
])

<button {{ $attributes->merge([
    'class' => 'btn',
]) }}
	@if ($title !== '') data-bs-toggle="{{ $toggle }}"
		data-bs-placement="{{ $placement }}"
		title="{{ $title }}" @endif>
	{{ $slot }}
</button>
