<div {{ $attributes->merge(['class' => 'd-flex flex-column flex-center gap-6 min-h-250px']) }}>
	@isset($icon)
		{{ $icon }}
	@endisset
	<p class="fs-6 text-muted">{{ $slot }}</p>
	@isset($options)
		<div class="d-flex flex-center gap-2">
			{{ $options }}
		</div>
	@endisset
</div>
