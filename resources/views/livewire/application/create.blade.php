<x-slot:page_title>Create Application</x-slot:page_title>
<x-slot:page_subtitle>Funeral Assistance System | CSWDO Taguig</x-slot:page_subtitle>
<div class="d-flex flex-column gap-6">
	@include('application.create.partials.client')
	@include('application.create.partials.beneficiary')
	@include('application.create.partials.relationship')
	@include('application.create.partials.documents')
	@include('application.create.partials.confirmation')
</div>
