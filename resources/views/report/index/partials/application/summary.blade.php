<div class="d-flex flex-column gap-6">
	<x-card>
		<div class="row">
			{{-- start::Table --}}
			<div class="col-12 col-lg-8 min-h-100">
				<x-slot:header>Applications Per Status</x-slot:header>
				<table class="table table-bordered">
					<thead>
						<tr>
							<th>Status</th>
							<th>Count</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($applicationsPerStatus as $applicationPerStatus)
							<tr>
								<td>{{ $applicationPerStatus['name'] }}</td>
								<td>{{ $applicationPerStatus['count'] }}</td>
							</tr>
						@endforeach
						<tr>
							<td class="fw-bold">Total Applications:</td>
							<td class="fw-bold">{{ $applicationsTotal }}</td>
						</tr>
					</tbody>
				</table>
			</div>
			{{-- end::Table --}}
			{{-- start::Chart --}}
			<div class="col-12 col-lg-4">
				<canvas id="applications-per-status"
					data-chart-data='@json($applicationsPerStatus->pluck('count'))'
					data-chart-labels='@json($applicationsPerStatus->pluck('name'))'
					data-chart-type="pie"
					data-empty="{{ $applicationsPerStatus->isEmpty() ? 'true' : 'false' }}"></canvas>
			</div>
			{{-- end::Chart --}}
		</div>
	</x-card>
</div>
