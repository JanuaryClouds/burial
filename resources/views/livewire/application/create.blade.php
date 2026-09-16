<x-slot:pageTitle>Create Application</x-slot:pageTitle>
<x-slot:pageSubTitle>Funeral Assistance System | CSWDO Taguig</x-slot:pageSubTitle>
<div class="d-flex flex-column gap-6">
	@include('application.create.partials.client')
	@include('application.create.partials.beneficiary')
	@include('application.create.partials.relationship')
	@include('application.create.partials.documents')
	@include('application.create.partials.confirmation')
</div>
