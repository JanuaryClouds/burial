@props([
    'name',
    'id' => null,
    'label' => null,
    'type' => 'text',
    'placeholder' => null,
    'value' => '',
    'required' => false,
    'helpText' => false,
    'disabled' => false,
    'readonly' => false,
    'min' => null,
    'max' => null,
    'autocomplete' => false,
    'errorname' => null,
])

@php
	$isInactive = $disabled ? ' bg-body text-gray-700' : '';
	if ($errorname == null) {
	    if (str_contains($name, '[')) {
	        $errorname = str_replace('[', '.', str_replace(']', '', $name));
	    } else {
	        $errorname = $name;
	    }
	}
@endphp

<div class="mb-3">
	@if ($label)
		@if (app()->hasDebugModeEnabled())
			<label for="{{ $id ?? $name }}"
				class="form-label"
				data-bs-toggle="tooltip"
				data-bs-placement="top"
				title="{{ 'Name: ' . $name . ', Error: ' . $errorname . ', Autocomplete: ' . ($autocomplete ? 'on' : 'off') }}">
				{{ $label }}{{ $required ? ' *' : '' }}
			</label>
		@else
			<label for="{{ $id ?? $name }}"
				class="form-label">
				{{ $label }}{{ $required ? ' *' : '' }}
			</label>
		@endif
	@endif
	<input type="{{ $type }}"
		{{ $attributes->merge(['class' => 'form-control' . $isInactive]) }}
		name="{{ $name ?? $id }}"
		{{ $id ? 'id=' . $id : 'id=' . $name }}
		value="{{ old($name, $value) }}"
		aria-describedby="helpId"
		{{ $placeholder ? 'placeholder=' . $placeholder : '' }}
		{{ $required ? 'required' : '' }}
		{{ $disabled ? 'disabled' : '' }}
		{{ $readonly ? 'readonly' : '' }}
		{{ $min ? 'min=' . $min : '' }}
		{{ $max ? 'max=' . $max : '' }}
		autocomplete="{{ $autocomplete ? 'on' : 'off' }}" />

	@error($errorname)
		<small class="form-text text-danger me-2">{{ $message }}.</small>
	@enderror
	@if ($helpText)
		<small id="helpId"
			class="form-text text-muted">
			{{ $helpText }}
		</small>
	@endif
</div>
