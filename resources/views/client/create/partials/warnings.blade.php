<div class="row">
	<div class="col-12 col-md-6">
		<x-callout class="bg-danger-subtle border-danger-subtle h-100">
			<x-slot:icon>
				<x-icon.font-awesome class="fa-triangle-exclamation text-danger fs-2" />
			</x-slot:icon>
			<x-slot:title>
				<p class="text-danger fs-6 fw-bold">This form has required fields</p>
			</x-slot:title>
			<p class="text-danger">All fields marked with an asterisk (*) are required to be filled out. Leave blank if not
				required and inapplicable.</p>
		</x-callout>
	</div>
	<div class="col-12 col-md-6 d-flex flex-grow">
		<x-callout class="bg-info-subtle border-info-subtle h-100">
			<x-slot:icon>
				<x-icon.font-awesome class="fa-info-circle text-info fs-2" />
			</x-slot:icon>
			<x-slot:title>
				<p class="text-info fs-6 fw-bold">Step-by-step Procedure</p>
			</x-slot:title>
			<p class="text-info">After submission, you will be redirected to filling out the beneficiary's information. This
				will be saved as a draft, allowing you to return and complete it at a later time.</p>
		</x-callout>
	</div>
</div>
