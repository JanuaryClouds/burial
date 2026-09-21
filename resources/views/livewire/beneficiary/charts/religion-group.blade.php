<div wire:poll.60s='refresh'>
	<livewire:chart chartId="{{ $chartId }}"
		:chartData="$religionGroups"
		chartTitle="{{ $chartTitle }}"
		chartType="{{ $chartType }}" />
</div>
