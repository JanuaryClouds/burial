<div class="d-flex flex-column gap-6">
	<div class="row">
		<div class="col-12 col-lg-4">
			<div class="d-flex flex-column gap-4">
				<x-card>
					<p class="mb-4">Use a barcode scanner or scan the QR Code to paste the application's tracking code. Alternatively,
						manually input
						the tracking code in the Code field below. </p>
					<x-form.input label="Code"
						name="code"
						wire:loading.attr='disabled'
						wire:model="code"
						wire:keydown.enter="search()" />
					<x-slot:footer>
						<x-button wire:click="clear()"
							class="btn-light">
							Clear
						</x-button>
						<x-button wire:click="search"
							class="btn-primary">
							Search
						</x-button>
					</x-slot:footer>
				</x-card>
				@if ($application)
					<x-card>
						<x-slot:header>Tracker Codes</x-slot:header>
						<div class="d-flex flex-column flex-center">
							<img src="{{ $qrCode }}"
								alt="{{ $application->qr_code }}"
								style="width: 200px; height: 200px;" />
							<img src="{{ $barcode }}"
								alt="{{ $application->qr_code }}"
								style="width: 300px; height: 100px;" />
						</div>
					</x-card>
				@endif
				<x-card>
					<x-slot:header>Tips</x-slot:header>
					<ol>
						<li>All tracking codes of applications from this system must start with <strong>"FUNERAL-"</strong></li>
						<li>Every application will have unique tracking code</li>
						<li>Both the QR Code and Barcode will output the same tracking code</li>
					</ol>
				</x-card>
			</div>
		</div>
		<div class="col-12 col-lg-8">
			@if ($application)
				<x-card>
					<x-slot:header>Results</x-slot:header>
					<h4 class="fs-4 fw-bold mb-4">{{ $application->tracking_no }}</h4>
					<h4 class="fs-5 fw-bold">Client</h4>
					@include('client.partials.create.form', [
						'client' => $application->client,
						'readonly' => true,
					])
					<div class="separator my-6"></div>
					<h4 class="fs-5 fw-bold">Beneficiary</h4>
					@include('beneficiary.partials.create.form', [
						'beneficiary' => $application->beneficiary,
						'readonly' => true,
					])
					<x-slot:footer>
						<a href="{{ route('application.show', $application) }}"
							class="btn btn-info"
							role="button">
							<i class="fa-solid fa-up-right-from-square"></i>
							View Application
						</a>
					</x-slot:footer>
				</x-card>
			@endif
		</div>
	</div>
</div>
