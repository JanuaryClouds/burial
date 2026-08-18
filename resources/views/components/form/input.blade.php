@props(['name', 'id' => null, 'label' => null, 'type' => 'text', 'helpText' => false, 'errorname' => null])

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
	wire:loading.remove>
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
	<input type="{{ $type }}"
		{{ $attributes->merge(['class' => 'form-control']) }}
		name="{{ $name ?? $id }}"
		{{ $id ? 'id=' . $id : 'id=' . $name }}
		aria-describedby="{{ $helpText ? 'helpId' : '' }}" />

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
