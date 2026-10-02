<div class="d-flex flex-column gap-6">
	<div class="row">
		{{-- start::Table --}}
		<div class="col-12 col-lg-8 h-100">
			<x-card>
				<x-slot:header>Clients Per Region</x-slot:header>
				<table class="table table-bordered">
					<thead>
						<tr>
							<th>Region</th>
							<th>Count</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($clientsPerRegion as $clientPerRegion)
							<tr>
								<td>{{ $clientPerRegion['region_name'] }}</td>
								<td>{{ $clientPerRegion['count'] }}</td>
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
	</div>
</div>
