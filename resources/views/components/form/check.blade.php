@props(['name', 'id' => null, 'label', 'checked' => false])

<div class="form-check form-check-custom form-check-solid">
	<input type="checkbox"
		name="{{ $name }}"
		id="{{ $id ?? $name }}"
		{{ $checked ? 'checked' : '' }}
		{{ $attributes->merge(['class' => 'form-check-input']) }} />
	<label class="form-check-label"
		for="{{ $id ?? $name }}">
		{{ $label }}
	</label>
</div>
