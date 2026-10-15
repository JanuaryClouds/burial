@props(['name', 'id' => null, 'label', 'checked' => false, 'value' => null])

<div class="form-check form-check-custom form-check-solid">
	<input type="checkbox"
		name="{{ $name }}"
		id="{{ $id ?? $name }}"
		value="{{ $value ?? $name }}"
		{{ $checked ? 'checked' : '' }}
		{{ $attributes->merge(['class' => 'form-check-input']) }} />
	<label class="form-check-label"
		for="{{ $id ?? $name }}">
		{{ $label }}
	</label>
</div>
