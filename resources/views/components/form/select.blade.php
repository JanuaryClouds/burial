@props(['name', 'id' => null, 'label' => null, 'options' => [], 'helpText' => false, 'errorname' => null])

@php
	if ($errorname == null) {
	    if (str_contains($name, '[')) {
	        $errorname = str_replace('[', '.', str_replace(']', '', $name));
	    } else {
	        $errorname = $name;
	    }
	}
@endphp

<div class="mb-3"
	wire:ignore>
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

	<select {{ $attributes->merge(['class' => 'form-control']) }}
		name="{{ $name }}"
		id="{{ $id ?? $name }}"
		data-control="select2">
		<option value="">Select one</option>
		@foreach ($options as $key => $value)
			<option value="{{ $key }}">{{ $value }}</option>
		@endforeach
	</select>
	@error($errorname)
		<span class="text-danger">{{ $message }}</span>
	@enderror
</div>
