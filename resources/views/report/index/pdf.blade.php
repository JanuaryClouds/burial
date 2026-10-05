<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport"
		content="width=device-width, initial-scale=1.0">
	<title>
		{{ $pageTitle }}
	</title>
	<style>
		body {
			font-size: 12px;
		}

		table {
			width: 100%;
			border-collapse: collapse;
			margin-top: 20px;
		}

		.text-center {
			text-align: center;
		}

		.title {
			font-weight: bold;
			font-size: 24px;
			text-transform: uppercase;
			font-family: serif;
		}

		.subtitle {
			font-size: 16px;
			text-transform: uppercase;
			font-family: serif;
		}

		.logo {
			width: 70%;
			height: auto;
		}

		.no-border {
			border: none !important;
		}

		th,
		td {
			border: 1px solid #000000;
			padding: 6px;
			text-align: left;
		}

		th {
			background-color: #f2f2f2;
		}

		.text-muted {
			color: #6c757d !important;
			font-size: 8px;
		}
	</style>
</head>

<body>
	<table>
		<tr>
			<td style="width: 30%; text-align: center;"
				class="no-border">
				<img src="./images/CSWDO.webp"
					alt=""
					class="logo">
			</td>
			<td class="no-border">
				<h1 class="title text-center">Taguig City CSWDO</h1>
				<p class="subtitle text-center"
					style="font-weight: bold;">Funeral Assistance</p>
				<h2 class="text-center"
					style="font-family: serif; text-transform: uppercase;">Clients Report</h2>
				<p class="text-center"
					style="font-family: serif;">
					{{ \Carbon\Carbon::parse($startDate)->format('F d, Y') }} to
					{{ \Carbon\Carbon::parse($endDate)->format('F d, Y') }}</p>
			</td>
			<td style="width: 30%; text-align: center;"
				class="no-border">
				<img src="./images/city_logo.webp"
					alt=""
					class="logo">
			</td>
		</tr>
	</table>
	<hr>

	<img src="{{ $applicationPerStatusUri }}"
		alt=""
		style="max-width:100%; height:auto;"
		class="text-center">

	{{-- start::Application Summary --}}
	<table>
		<tbody>
			<td class="no-border"
				style="min-width: 60%;">
				{{-- start::Application Summary --}}
				<table class="table">
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
				{{-- end::Application Summary --}}
			</td>
			{{-- start::Applications Per Status Chart --}}
			<td class="no-border"
				style="max-width: 40%;">
				<img src="{{ $applicationPerStatusUri }}"
					alt="Applications Per Status"
					style="max-width:100%; height:auto;"
					class="text-center">
			</td>
			{{-- end::Applications Per Status Chart --}}
		</tbody>
	</table>
	{{-- end::Application Summary --}}

	{{-- start::Beneficiary Summary --}}
	<table>
		<tbody>
			{{-- start::Beneficiary Statistics --}}
			<td class="no-border"
				style="min-width: 60%">
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
			</td>
			{{-- end::Beneficiary Statistics --}}
			{{-- start::Beneficiary Per Age Group Chart --}}
			<td class="no-border"
				style="max-width: 40%;">
				<img src="{{ $beneficiaryPerAgeGroupUri }}"
					alt="Beneficiary Per Age Group"
					style="max-width:100%; height:auto;"
					class="text-center">
			</td>
			{{-- end::Beneficiary Per Age Group Chart --}}
		</tbody>
	</table>
	{{-- end::Beneficiary Summary --}}

	{{-- start::Clients Per Region --}}
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
	{{-- end::Clients Per Region --}}

	{{-- start::Applications --}}
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
	{{-- end::Applications --}}

	<table>
		<tbody>
			<tr>
				<td class="no-border text-muted">
					Report Generated at {{ \Carbon\Carbon::now()->toISOString() }}
				</td>
				<td class="no-border text-muted"
					style="text-align: right;">
					Generated by
					{{ Auth::user()?->first_name ?? 'System' }}
					{{ Auth::user()?->last_name ?? '' }}
				</td>
			</tr>
		</tbody>
	</table>
</body>

</html>
