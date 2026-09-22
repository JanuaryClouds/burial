<div wire:poll.60s='refresh'>
	<livewire:chart :chartId="$chartId"
		:chartType="$chartType"
		:chartTitle="$chartTitle"
		:chartData="$perMonth" />
</div>
