<div class="d-flex flex-column gap-6">
	<div class="row">
		{{-- start::Counts Table --}}
		<div class="col-12 col-lg-8 h-100">
			<x-card>
				<x-slot:header>Beneficiary Statistics</x-slot:header>
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
			</x-card>
		</div>
		{{-- end::Counts Table --}}
		<div class="col-12 col-lg-4 h-100">
			<x-card>
				<x-slot:header>Beneficiaries per Age Group</x-slot:header>
				<livewire:beneficiary.charts.per-age-group :startDate="$startDate"
					:endDate="$endDate" />
			</x-card>
		</div>
	</div>
	{{-- end::Counts Table --}}

	{{-- start::Per Age Group Chart --}}
	{{-- end::Per Age Group Chart --}}
</div>
