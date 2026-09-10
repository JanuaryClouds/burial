@props(['name', 'id' => null, 'label' => null, 'required' => false])

<div class="mb-3"
	wire:loading.remove>
	<label for="{{ $name ?? $id }}"
		class="form-label {{ $required ? 'required' : '' }}">{{ $label }}</label>
	<textarea {{ $attributes->merge(['class' => 'form-control']) }}
	 name="{{ $name }}"
	 id="{{ $id ?? $name }}"
	 rows="3"></textarea>
</div>
