@props(['application', 'qrCode', 'barcode'])
<x-card>
	<x-slot:header>Tracking Codes</x-slot:header>
	<div class="d-flex flex-column flex-center">
		<img src="{{ $qrCode }}"
			alt="{{ $application->qr_code }}"
			style="width: 200px; height: 200px;" />
		<img src="{{ $barcode }}"
			alt="{{ $application->qr_code }}"
			style="width: 300px; height: 100px;" />
	</div>
	<x-slot:footer>
		<a class="btn btn-sm btn-primary"
			target="_blank"
			href="{{ route('application.codes', $application) }}"
			role="button">
			<i class="fa-solid fa-print"></i>
			Print
		</a>
	</x-slot:footer>
</x-card>
