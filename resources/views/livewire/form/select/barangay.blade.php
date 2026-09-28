<x-form.select wire:model.live='{{ $name }}'
	wire:key='barangaySelect'
	name="{{ $name }}"
	label="{{ $label }}"
	helpText="{{ $helpText }}"
	:selected="$selected"
	:options="$options"
	:required="$required" />
