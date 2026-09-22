<div wire:poll.60s='refresh'>
	<livewire:chart :chartId="$chartId"
		:chartData="$perRelationship"
		:chartTitle="$chartTitle"
		:chartType="$chartType" />
</div>
