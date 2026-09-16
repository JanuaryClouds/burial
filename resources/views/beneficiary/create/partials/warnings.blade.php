<x-card>
	<div class="row">
		<div class="col-12 col-md-6">
			<x-callout class="bg-danger-subtle border-danger-subtle h-100">
				<x-slot:icon>
					<x-icon.font-awesome class="fa-triangle-exclamation text-danger fs-2" />
				</x-slot:icon>
				<x-slot:title>
					<p class="text-danger fs-6 fw-bold">This from has required fields</p>
				</x-slot:title>
				<p class="text-danger">All fields marked with an asterisk (*) are required to be filled out.</p>
			</x-callout>
		</div>
		<div class="col-12 col-md-6 d-flex flex-grow">
			<x-callout class="bg-info-subtle border-info-subtle h-100">
				<x-slot:icon>
					<x-icon.font-awesome class="fa-info-circle text-info fs-2" />
				</x-slot:icon>
				<x-slot:title>
					<p class="text-info fs-6 fw-bold">
						Step-by-step Procedure
					</p>
				</x-slot:title>
				<p class="text-info">After submission, you will be redirected to filling out the Application Builder. You will be
					required to submit photos of the required documents mentioned during your client registration procedure.</p>
			</x-callout>
		</div>
	</div>
</x-card>
