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
	<table style="margin-top: -3rem;">
		<tr>
			<td style="width: 25%; text-align: center;"
				class="no-border">
				<img src="./images/CSWDO.webp"
					alt=""
					class="logo">
			</td>
			<td class="no-border text-center">
				<p class="bold"
					style="font-family: serif; font-size: 1rem; text-transform: uppercase; font-weight: bold; letter-spacing: 0.1rem;">
					Republika ng Pilipinas<br />
					Lungsod ng Taguig<br />
					Tanggapang Panlungsod sa Kagalingang Panlipunan at Pagpapaunlad</p>
				<p class="text-center"
					style="font-family: serif; font-size: 1rem; text-transform: uppercase; font-weight: bold; letter-spacing: 0.1rem;">
					FUNERAL ASSISTANCE REPORT</p>
				<p class="text-center"
					style="font-family: serif;">
					{{ \Carbon\Carbon::parse($startDate)->format('F d, Y') }} to
					{{ \Carbon\Carbon::parse($endDate)->format('F d, Y') }}</p>
			</td>
			<td style="width: 25%; text-align: center;"
				class="no-border">
				<img src="./images/city_logo.webp"
					alt=""
					class="logo">
			</td>
		</tr>
	</table>
	<hr>

	<h1>Applications Summary</h1>
	{{-- start::Application Summary --}}
	<table>
		<tbody>
			{{-- start::Applications Per Status Chart --}}
			<td class="no-border"
				style="width: 30%; text-align: center;">
				<p>Applications Per Status</p>
				<div class="chart">
					<img src="{{ $charts['applications-per-status'] }}"
						alt="Applications Per Status"
						style="max-width:100%; height:auto;"
						class="text-center">
				</div>
			</td>
			{{-- end::Applications Per Status Chart --}}
			<td class="no-border"
				style="min-width: 70%;">
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
		</tbody>
	</table>
	{{-- end::Application Summary --}}
	<br />

	<h1>Beneficiaries Summary</h1>
	{{-- start::Beneficiary Summary --}}
	<table>
		<tbody>
			{{-- start::Beneficiary Per Age Group Chart --}}
			<td class="no-border"
				style="width: 30%; text-align: center;">
				<p>Beneficiaries Per Age Group</p>
				<div class="chart">
					<img src="{{ $charts['beneficiaries-per-age-group'] }}"
						alt="Beneficiaries Per Age Group"
						style="max-width:100%; height:auto;"
						class="text-center">
				</div>
			</td>
			{{-- end::Beneficiary Per Age Group Chart --}}
			{{-- start::Beneficiary Statistics --}}
			<td class="no-border"
				style="min-width: 70%">
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
		</tbody>
	</table>
	{{-- end::Beneficiary Summary --}}
	<br />

	<h1>Clients Per Region</h1>
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
	<br />

	<h1>Applications</h1>
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
