<div {{ $attributes->merge(['class' => 'd-flex justify-content-start gap-2 py-4 px-5 border-start border-5']) }}>
	@isset($icon)
		<div>
			{{ $icon }}
		</div>
	@endisset
	<div class="d-flex flex-column">
		@isset($title)
			{{ $title }}
		@endisset
		<div>
			{{ $slot }}
		</div>
	</div>
</div>
