<div class="row">
	<div class="col-12 col-md-6">
		<x-card>
			<x-slot:header>Applications Per Status</x-slot:header>
			<x-chart chartId='applications-per-status'
				:chartData="$applicationsPerStatus"
				chartType='pie' />
		</x-card>
	</div>
	<div class="col-12 col-md-6">
		<x-card>
			<x-slot:header>Applications Per Relationship</x-slot:header>
			<x-chart chartId='applications-per-relationship'
				:chartData="$applicationsPerRelationship"
				chartType='bar' />
		</x-card>
	</div>
</div>
