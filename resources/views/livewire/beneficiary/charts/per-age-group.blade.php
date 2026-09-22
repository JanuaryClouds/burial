<div wire:poll.60s="refresh">
	<livewire:chart :chartId="$chartId"
		:chartData="$perAgeGroups"
		:chartTitle="$chartTitle"
		:chartType="$chartType" />
</div>
