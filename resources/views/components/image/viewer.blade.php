<div class="d-flex flex-column gap-4 rounded border border-2 bg-gray-100 border-gray-200 px-6 py-3">
	<div class="d-flex justify-content-between align-items-center">
		<h5 class="mb-0">
			{{ Str::title(Str::replace('_', ' ', $alt)) }}
		</h5>
		<x-button
			data-image-viewer-url="{{ $src . '/webView' }}"
			class="btn-sm btn-primary"
			title="View on a new Window"
			toggle="tooltip"
			placement="bottom">
			<x-icon.font-awesome class="fa-external-link pe-0" />
		</x-button>
	</div>
	<img src="{{ $src }}"
		alt="{{ $alt }}"
		class="img-thumbnail object-fit-contain"
		style="max-width: 100%; max-height: 300px">
</div>
