@extends('layouts.app')
@section('content')
	<div class="d-flex flex-column gap-4">
		@can('office-access')
			<div class="card multicolor-border">
				<div class="card-header">
					<h5 class="card-title">All Interviews</h5>
				</div>
				<div class="card-body">
					<x-data-table :columns="$allDataColumns" :data="$allData" data-table-id="interview-all"
                        :card-fields="['primary' => 'client', 'secondary' => 'remarks', 'badge' => 'status', 'date' => 'schedule']" />
				</div>
			</div>
		@else
			<div class="card multicolor-border">
				<div class="card-header">
					<h5 class="card-title">My Interviews</h5>
				</div>
				<div class="card-body">
					<x-data-table :columns="$personalDataColumns" :data="$personalData" data-table-id="interview-personal"
                        :card-fields="['primary' => 'client', 'secondary' => 'remarks', 'badge' => 'status', 'date' => 'schedule']" />
				</div>
			</div>
		@endcan
	</div>
@endsection
