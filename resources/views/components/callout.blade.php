<div {{ $attributes->merge(['class' => 'd-flex justify-content-start w-100 gap-2 py-4 px-5 border-start border-5']) }}>
	@isset($icon)
		<div>
			{{ $icon }}
		</div>
	@endisset
	<div class="d-flex flex-column">
		@isset($title)
			<p class="fw-bold">
				{{ $title }}
			</p>
		@endisset
		<div>
			{{ $slot }}
		</div>
	</div>
</div>
