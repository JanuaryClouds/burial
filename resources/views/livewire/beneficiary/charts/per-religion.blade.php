<div wire:poll.60s='refresh'>
	<livewire:chart :chartId="$chartId"
		:chartData="$perReligion"
		:chartTitle="$chartTitle"
		:chartType="$chartType" />
</div>
