@props(['label' => null])

<div>
	@if ($label)
		<div class="form-label fw-semibold">{{ $label }}</div>
	@endif
	<input type="file"
		accept="image/jpeg,image/png"
		class="form-control"
		{{ $attributes }}>

	@error($attributes->wire('model')->value())
		<p class="text-danger mt-2">{{ $message }}</p>
	@enderror
</div>
