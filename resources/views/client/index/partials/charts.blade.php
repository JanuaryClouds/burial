<div class='row'>
	@can('office-access')
		<div class="col-12 col-md-6">
			<x-card>
				<x-slot:header>Clients Per Month</x-slot:header>
				<x-chart chartId="clients-per-month"
					:chartData="$clientsPerMonth"
					chartType="line" />
			</x-card>
		</div>
	@endcan
</div>
