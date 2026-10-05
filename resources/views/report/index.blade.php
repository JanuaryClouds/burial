@extends('layouts.app')
@section('content')
	<div class="d-flex flex-column gap-6">
		@include('reports.partials.filter')
		@include('reports.partials.export-to-pdf', [
			'startDate' => $startDate,
			'endDate' => $endDate,
		])
	</div>
@endsection
