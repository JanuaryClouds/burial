@props([
    'name',
    'id' => null,
    'label' => null,
    'selected' => null,
    'options' => [],
    'helpText' => false,
    'errorname' => null,
    'required' => false,
    'readonly' => false,
    'multiple' => false,
])

@php
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
				class="form-label {{ $required ? 'required' : '' }}"
				data-bs-toggle="tooltip"
				data-bs-placement="top"
				title="{{ 'Name: ' . $name . ', Error: ' . $errorname }}">
				{{ $label }}
			</label>
		@else
			<label for="{{ $id ?? $name }}"
				class="form-label {{ $required ? 'required' : '' }}">
				{{ $label }}
			</label>
		@endif
	@endif

	<div>
		<select {{ $attributes->whereStartsWith('wire:model') }}
			name="{{ $name }}_select"
			id="{{ $id ?? $name }}_select"
			{{ $required ? 'required' : '' }}
			{{ $readonly ? 'disabled' : '' }}
			{{ $attributes->whereDoesntStartWith('wire:model')->merge(['class' => 'form-control' . ($readonly ? ' bg-light' : '')]) }}
			data-control="select2"
			{{ $multiple ? "multiple='multiple'" : '' }}>
			<option value="">Select one</option>
			@foreach ($options as $key => $value)
				<option value="{{ $key }}"
					{{ $selected == $key ? 'selected' : '' }}>
					{{ $value }}
				</option>
			@endforeach
		</select>
	</div>
	{{-- Display labels are not automatically Livewire properties. Address forms
	     populate their labels in their selection hooks. Keep the native form
	     field without inventing e.g. demographicsForm.sexId_display bindings. --}}
	<input type="hidden"
		name="{{ $name }}_display"
		id="{{ $id ?? $name }}_display">
	<input type="hidden"
		name="{{ $name }}"
		id="{{ $id ?? $name }}"
		value="{{ $selected }}"
		{{ $attributes->whereStartsWith('wire:model') }}>
	@error($errorname)
		<span class="text-danger">{{ $message }}</span>
	@enderror
	@if (app()->hasDebugModeEnabled())
		<span id="debug-selected-{{ $id ?? $name }}"
			class="text-muted text-small"></span>
	@endif
</div>
