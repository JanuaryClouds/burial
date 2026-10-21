<div wire:ignore
	class="bg-white px-4 py-3 rounded-3">
	<canvas id="{{ $chartId }}"
		data-chart-data='@json($chartData->pluck('count'))'
		data-chart-labels='@json($chartData->pluck('name'))'
		data-chart-type="{{ $chartType }}"
		data-chart-title="{{ $chartTitle }}"></canvas>
</div>
