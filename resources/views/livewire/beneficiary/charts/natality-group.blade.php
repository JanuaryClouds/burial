<div wire:poll.60s="refresh">
	<livewire:chart chartId="{{ $chartId }}"
		:chartData="$natalityGroups"
		chartTitle="{{ $chartTitle }}"
		chartType="{{ $chartType }}" />
</div>
