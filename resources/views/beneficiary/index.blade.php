@extends('layouts.app')
@section('content')
	<x-card>
		@include('partials.datatable.index', [
			'src' => 'data',
			'columns' => $columns,
		])
		@unlessrole('staff')
			<x-slot:footer>
				<a href="{{ route('beneficiary.create') }}"
					class="btn btn-sm btn-light">
					<i class="fa fa-plus"></i>
					Register a New Beneficiary
				</a>
			</x-slot:footer>
		@endunlessrole
	</x-card>
	<div class="row">
		<div class="col-12 col-lg-6 col-xl-4">
			<x-card>
				<x-slot:header>Beneficiaries Per Age Group</x-slot:header>
				<livewire:beneficiary.charts.age-group />
			</x-card>
		</div>
		<div class="col-12 col-lg-6 col-xl-4">
			<x-card>
				<x-slot:header>Perinatal and Neonatal Deaths</x-slot:header>
				<livewire:beneficiary.charts.natality-group />
			</x-card>
		</div>
		<div class="col-12 col-lg-6 col-xl-4">
			<x-card>
				<x-slot:header>Beneficiaries Per Religion</x-slot:header>
				<livewire:beneficiary.charts.religion-group />
			</x-card>
		</div>
	</div>
	<div class="row">
		<div class="col-6 col-lg-3">
			<livewire:beneficiary.charts.pwd-count />
		</div>
	</div>
@endsection
