@props(['name', 'id' => null, 'label' => null, 'selected' => null, 'options' => [], 'helpText' => false, 'errorname' => null])

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
				class="form-label"
				data-bs-toggle="tooltip"
				data-bs-placement="top"
				title="{{ 'Name: ' . $name . ', Error: ' . $errorname }}">
				{{ $label }}
			</label>
		@else
			<label for="{{ $id ?? $name }}"
				class="form-label">
				{{ $label }}
			</label>
		@endif
	@endif

	<div wire:ignore>
		<select {{ $attributes->except('wire:model') }}
			name="{{ $name }}_display"
			id="{{ $id ?? $name }}_display"
			class="form-control"
			data-control="select2">
			<option value="">Select one</option>
			@foreach ($options as $key => $value)
				<option value="{{ $key }}" {{ $selected == $key ? 'selected' : '' }}>{{ $value }}</option>
			@endforeach
		</select>
	</div>
	<input type="hidden"
		name="{{ $name }}"
		id="{{ $id ?? $name }}"
		value="{{ $selected }}"
		{{ $attributes->whereStartsWith('wire:model') }}>
	@error($errorname)
		<span class="text-danger">{{ $message }}</span>
	@enderror
	@if (app()->hasDebugModeEnabled())
		<span id="debug-selected-{{ $id ?? $name }}" class="text-muted text-small"></span>
	@endif
</div>
