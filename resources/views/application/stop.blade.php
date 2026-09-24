@extends('layouts.app')
@section('content')
	<div class="d-flex flex-column gap-6">
		{{-- start::Warning --}}
		<x-card>
			<x-slot:header>Warning</x-slot:header>
			<div class="row">
				<div class="col-12 col-lg-6">
					<x-callout class="bg-danger-subtle border-danger text-danger">
						<x-slot:icon>
							<x-icon.font-awesome class="fa-triangle-exclamation text-danger fs-2" />
						</x-slot:icon>
						<x-slot:title>
							Stopping an Application is an Irreversible Action
						</x-slot:title>
						Stopping an application will record the application as either cancelled, rejected, or referred. After of which, it
						cannot be undone. Please make sure you are certain with continuing this action.
					</x-callout>
				</div>
				<div class="col-12 col-lg-6">
					<x-callout class="bg-info-subtle border-info text-info h-100">
						<x-slot:icon>
							<x-icon.font-awesome class="fa-info-circle text-info fs-2" />
						</x-slot:icon>
						<x-slot:title>
							Permissions
						</x-slot:title>
						Only clients can cancel their application. CSWDO are authorized to reject or refer an application.
					</x-callout>
				</div>
			</div>
		</x-card>
		{{-- end::Warning --}}

		<div class="row">
			<div class="col-12 col-lg-6">
				{{-- start::Cancellation --}}
				@can('cancel', [$application])
					<x-card>
						<x-slot:header>Cancel Application</x-slot:header>
						<livewire:cancellation.create :application="$application"
							defer />
					</x-card>
				@endcan
				{{-- end::Cancellation --}}
			</div>
			<div class="col-auto col-lg-6">
				<x-card>
					<x-slot:header>Reject or Refer Application</x-slot:header>
					<livewire:application.stop :application="$application"
						defer />
				</x-card>
			</div>
		</div>
	</div>
@endsection
