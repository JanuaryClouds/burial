<div wire:poll.60s="refresh">
	<livewire:chart :chartId="$chartId"
		:chartData="$perNatality"
		:chartTitle="$chartTitle"
		:chartType="$chartType" />
</div>
