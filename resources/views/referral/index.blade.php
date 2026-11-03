@extends('layouts.app')
@section('content')
	<div class="d-flex flex-column gap-4">
		@can('office-access')
			<div class="card multicolor-border">
				<div class="card-header">
					<h5 class="card-title">All Referrals</h5>
				</div>
				<div class="card-body">
					<x-data-table :columns="$allDataColumns" :data="$allData" data-table-id="referral-all"
                        :card-fields="['primary' => 'client', 'secondary' => 'referral_to', 'tertiary' => 'remarks']" />
				</div>
			</div>
		@else
			<div class="card multicolor-border">
				<div class="card-header">
					<h5 class="card-title">My Referrals</h5>
				</div>
				<div class="card-body">
					<x-data-table :columns="$personalDataColumns" :data="$personalData" data-table-id="referral-personal"
                        :card-fields="['primary' => 'client', 'secondary' => 'referral_to', 'tertiary' => 'remarks']" />
				</div>
			</div>
		@endcan
	</div>
@endsection
