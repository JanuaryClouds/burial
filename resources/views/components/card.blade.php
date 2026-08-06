<div class="card multicolor-border">
	@isset($header)
		<div class="card-header">
			<h5 class="card-title">{{ $header }}</h5>
		</div>
	@endisset
	<div class="card-body">
		{{ $slot }}
	</div>
	@isset($footer)
		<div class="card-footer d-flex justify-content-end gap-2">
			{{ $footer }}
		</div>
	@endisset
</div>
