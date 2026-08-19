<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport"
		content="width=device-width, initial-scale=1.0">
	<title>Tracking Slip for {{ $application->tracking_no }}</title>
	<style>
		@page {
			margin: 0px 0px 0px 0px;
		}

		body {
			font-size: 12px;
			font-family: Calibri;
		}

		.bold {
			font-weight: bold;
		}

		.underline {
			text-decoration: underline;
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

		.title {
			font-weight: bold;
			font-size: 24px;
			text-transform: uppercase;
			font-family: serif;
		}

		.text-center {
			text-align: center;
		}

		.text-muted {
			color: #6c757d !important;
			font-size: 8px;
		}

		th {
			background-color: #f2f2f2;
		}

		table {
			width: 100%;
			border-collapse: collapse;
			margin-top: 20px;
		}

		th,
		td {
			border: 1px solid #000000;
			padding: 6px;
			text-align: left;
		}

		p {
			font-family: 'sans-serif';
			font-size: 14px;
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
			<td class="no-border text-center">
				<p style="font-size: 8px;">Republika ng Pilipinas<br />
					Lungsod ng Taguig<br />
					Tanggapang Panlungsod sa Kagalingang Panlipunan at Pagpapaunlad</p>
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
	<div>
		<p class="bold"
			style="text-align: center; text-transform: uppercase; font-size: 14px;">
			FUNERAL ASSISTANCE PROGRAM<br />
			TRACKING SLIP
		</p>
		<p class="bold"
			style="text-align: center; text-transform: uppercase; font-size: 14px;">
			{{ $application->qr_code }}
		</p>
	</div>
	<table>
		<tr>
			<td class="text-center no-border"
				style="vertical-align: middle; width: 50%;">
				<img src="{{ $qrCode }}"
					alt="{{ $application->qr_code }}"
					style="width: 200px; height: 200px;">
			</td>
		</tr>
		<tr>
			<td class="text-center no-border"
				style="vertical-align: middle; width: 50%;">
				<img src="{{ $barcode }}"
					alt="{{ $application->qr_code }}"
					style="width: 200px; height: 50px;" />
			</td>
		</tr>
	</table>
	<div style="width: 100%; background-color: #ED1C24; color: white;">
		<p class="bold"
			style="text-align: center; font-size: 12px; margin: 4px 0px 0px 0px;">DO NOT DISPOSE</p>
		<p class=""
			style="text-align: center; font-size: 12px; margin: 0px 12px 1px 12px;">
			Keep this slip with the application documents until processing is complete
		</p>
	</div>
</body>

</html>
