<div wire:poll.60s='refresh'>
	<livewire:chart :chartId="$chartId"
		:chartData="$perMonth"
		:chartTitle="$chartTitle"
		:chartType="$chartType" />
</div>
