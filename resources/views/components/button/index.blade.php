<button {{ $attributes->merge([
    'class' => 'btn',
]) }}
	@if (isset($title)) data-bs-toggle="{{ $toggle }}"
		data-bs-placement="{{ $placement }}"
		title="{{ $title }}" @endif>
	{{ $slot }}
</button>
