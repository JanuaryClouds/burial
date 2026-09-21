<div wire:poll.60s="refresh">
	<livewire:chart chartId="{{ $chartId }}"
		:chartData="$ageGroups"
		chartTitle="{{ $chartTitle }}"
		chartType="{{ $chartType }}" />
</div>
