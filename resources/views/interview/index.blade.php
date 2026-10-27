@extends('layouts.app')
@section('content')
	<div class="d-flex flex-column gap-4">
		@can('office-access')
			<div class="card multicolor-border">
				<div class="card-header">
					<h5 class="card-title">All Interviews</h5>
				</div>
				<div class="card-body">
					@include('partials.dataTable.index', [
						'columns' => $allDataColumns,
						'data' => $allData,
						'src' => 'allData',
					])
				</div>
			</div>
		@else
			<div class="card multicolor-border">
				<div class="card-header">
					<h5 class="card-title">My Interviews</h5>
				</div>
				<div class="card-body">
					@include('partials.dataTable.index', [
						'columns' => $personalDataColumns,
						'data' => $personalData,
						'src' => 'personalData',
					])
				</div>
			</div>
		@endcan
	</div>
@endsection
