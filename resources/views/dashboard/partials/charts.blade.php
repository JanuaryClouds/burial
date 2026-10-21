<div class='row'>
	<div class="col-12 col-lg-6">
		<x-card>
			<x-slot:header>Applications Per Status</x-slot:header>
			<x-chart chartId="applications-per-status"
				chartType="pie"
				:chartData="$applicationsPerStatus" />
		</x-card>
	</div>
	<div class="col-12 col-lg-6">
		<x-card>
			<x-slot:header>Applications Per Relationship</x-slot:header>
			<x-chart chartId="applications-per-relationship"
				chartType="bar"
				:chartData="$applicationsPerRelationship" />
		</x-card>
	</div>
</div>
