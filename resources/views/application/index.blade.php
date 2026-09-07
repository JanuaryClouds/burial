@extends('layouts.app')
@section('content')
	<x-card>
		@include('partials.datatable.index', [
			'columns' => $columns,
			'src' => 'data',
		])
		<x-slot:footer>
			<a href="{{ route('application.search') }}"
				class="btn btn-sm btn-primary"
				role="button">
				<x-icon.font-awesome class="fa-qrcode" />
				Scan Barcode
			</a>
			@unlessrole('staff')
				<a href="{{ route('application.create') }}"
					class="btn btn-sm btn-light">
					<i class="fa fa-plus"></i>
					Create a New Application
				</a>
			@endunlessrole
		</x-slot:footer>
	</x-card>
@endsection
