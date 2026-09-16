<div class="d-flex flex-column">
	<div>
		<small class="text-muted">{{ $label }}</small>
	</div>
	<div class="bg-gray-100 rounded px-4 py-3 w-auto">
		{{ $contents }}
	</div>
	@isset($helpText)
		<small>{{ $helpText }}</small>
	@endisset
</div>
