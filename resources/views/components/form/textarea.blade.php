@props(['name', 'id' => null, 'label' => null])

<div class="mb-3"
	wire:loading.remove>
	<label for="{{ $name ?? $id }}"
		class="form-label">{{ $label }}</label>
	<textarea {{ $attributes->merge(['class' => 'form-control']) }}
	 name="{{ $name }}"
	 id="{{ $id ?? $name }}"
	 rows="3"></textarea>
</div>
