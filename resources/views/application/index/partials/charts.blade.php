<div class="row">
	<div class="col-12 col-md-6">
		<x-card>
			<x-slot:header>Applications Per Status</x-slot:header>
			<livewire:application.charts.per-status :applications="$applications" />
		</x-card>
	</div>
	<div class="col-12 col-md-6">
		<x-card>
			<x-slot:header>Applications Per Relationship</x-slot:header>
			<livewire:application.charts.per-relationship :applications="$applications" />
		</x-card>
	</div>
</div>
