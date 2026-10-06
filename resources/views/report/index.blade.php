@extends('layouts.app')
@section('content')
	<div class="d-flex flex-column gap-6">
		{{-- start::Filter --}}
		@include('reports.partials.filter')
		{{-- end::Filter --}}

		{{-- start::Applications Summary --}}
		@include('report.index.partials.application.summary')
		{{-- end::Applications Summary --}}

		{{-- start::Beneficiaries Summary --}}
		@include('report.index.partials.beneficiary.summary')
		{{-- end::Beneficiaries Summary --}}

		{{-- start::Clients Per Region --}}
		@include('report.index.partials.client.per-region')
		{{-- end::Clients Per Region --}}

		{{-- start::Applications Table --}}
		@include('report.index.partials.application.table')
		{{-- end::Applications Table --}}

		{{-- start::Export Button --}}
		<div class="d-flex flex-center">
			<div>
				@include('reports.partials.export-to-pdf', [
					'startDate' => $startDate,
					'endDate' => $endDate,
				])
			</div>
		</div>
		{{-- end::Export Button --}}
	</div>
@endsection
