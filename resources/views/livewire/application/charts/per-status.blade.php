<div wire:poll.60s="refresh">
	<livewire:chart :chartData="$perStatus"
		:chartId="$chartId"
		:chartType="$chartType"
		:chartTitle="$chartTitle" />
</div>
