<div class="d-flex flex-column gap-6">
	<x-card>
		<x-slot:header>Beneficiary Summary</x-slot:header>
		<div class="row">
			{{-- start::Counts Table --}}
			<div class="col-12 col-lg-8">
				<table class="table table-bordered">
					<thead>
						<tr>
							<th>Description</th>
							<th>Count</th>
						</tr>
					</thead>
					<tbody>
						<tr>
							<td class="fw-bold">Total Beneficiaries:</td>
							<td class="fw-bold">{{ $beneficiariesTotal }}</td>
						</tr>
						<tr>
							<td>Total PWD Beneficiaries:</td>
							<td>{{ $beneficiariesPwd }}</td>
						</tr>
						@foreach ($beneficiariesNatality as $group)
							<tr>
								<td>{{ $group['group'] }}</td>
								<td>{{ $group['count'] }}</td>
							</tr>
						@endforeach
					</tbody>
				</table>
			</div>
			{{-- end::Counts Table --}}
			{{-- start::Per Age Group Chart --}}
			<div class="col-12 col-lg-4">
				<canvas id="beneficiaries-per-age-group"
					data-chart-data='@json($beneficiariesAgeGroups->pluck('count'))'
					data-chart-labels='@json($beneficiariesAgeGroups->pluck('name'))'
					data-chart-type="pie"
					data-empty="{{ $beneficiariesAgeGroups->isEmpty() ? 'true' : 'false' }}"></canvas>
			</div>
		</div>
	</x-card>
</div>
