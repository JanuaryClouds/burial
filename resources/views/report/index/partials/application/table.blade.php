<div>
	<div class="row">
		<div class="col-12">
			<x-card>
				<x-slot:header>Applications</x-slot:header>
				<table class="table table-bordered">
					<thead>
						<tr>
							<th>Tracking Number</th>
							<th>Client</th>
							<th>Relationship with Beneficiary</th>
							<th>Beneficiary</th>
							<th>Age</th>
							<th>PWD</th>
							<th>Status</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($applications as $application)
							<tr>
								<td>{{ $application['tracking_number'] }}</td>
								<td>{{ $application['client'] }}</td>
								<td>{{ $application['relationship_with_beneficiary'] }}</td>
								<td>{{ $application['beneficiary'] }}</td>
								<td>{{ $application['age'] }}</td>
								<td>{{ $application['PWD'] }}</td>
								<td>{{ ucwords($application['status']) }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</x-card>
		</div>
	</div>
</div>
