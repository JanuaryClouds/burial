@extends('layouts.app')
@section('content')
	<x-card>
		<div class="d-flex justify-content-between align-items-center">
			<h4>Applications</h4>
			<a href="{{ route('application.index') }}"
				class="fs-4 btn btn-primary">
				Total Applications : {{ \App\Models\Application::total()->get()?->count() }}
			</a>
		</div>
	</x-card>

	{{-- start::Statistics --}}
	@include('dashboard.partials.statistics')
	{{-- end::Statistics --}}

	@unlessrole('staff')
		<x-card>
			@include('partials.datatable.index', [
				'columns' => $columns,
				'src' => 'data',
			])
		</x-card>
	@endunlessrole

	@role('staff')
		{{-- start::Charts --}}
		@include('dashboard.partials.charts')
		{{-- end::Charts --}}
	@endrole
@endsection
