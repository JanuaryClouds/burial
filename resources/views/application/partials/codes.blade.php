@props(['application', 'qrCode', 'barcode'])
<div class="d-flex flex-column flex-center">
	<img src="{{ $qrCode }}"
		alt="{{ $application->qr_code }}"
		style="width: 200px; height: 200px;" />
	<img src="{{ $barcode }}"
		alt="{{ $application->qr_code }}"
		style="width: 300px; height: 100px;" />
</div>
