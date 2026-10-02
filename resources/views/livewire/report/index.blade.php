<div class="d-flex flex-column gap-6">
	{{-- start::Filter --}}
	<x-card>
		<x-slot:header>Filter</x-slot:header>
		@include('report.index.partials.filter')
	</x-card>
	{{-- end::Filter --}}

	{{-- start::Application Summary --}}
	@include('report.index.partials.application.summary')
	{{-- end::Application Summary --}}

	{{-- start::Beneficiary Summary --}}
	@include('report.index.partials.beneficiary.summary')
	{{-- end::Beneficiary Summary --}}

	{{-- start::Beneficiary Per Barangay Distribution --}}
	@include('report.index.partials.client.per-region')
	{{-- end::Beneficiary Per Barangay Distribution --}}

	{{-- start::Application Details --}}
	@include('report.index.partials.application.table')
	{{-- end::Application Details --}}
</div>
