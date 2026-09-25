<div class="d-flex flex-column gap-6">
	<div class="row">
		{{-- start::Table --}}
		<div class="col-12 col-lg-8 h-100">
			<x-card>
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
			</x-card>
		</div>
		{{-- end::Table --}}
		<div class="col-12 col-lg-4">
			{{-- start::Chart --}}
			<x-card>
				<x-slot:header>Applications Per Status Chart</x-slot:header>
				<livewire:application.charts.per-status :startDate="$startDate"
					:endDate="$endDate" />
			</x-card>
			{{-- end::Chart --}}
		</div>
	</div>
</div>
