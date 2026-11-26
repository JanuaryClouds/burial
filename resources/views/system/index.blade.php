@extends('layouts.app')
@section('content')
	<div class="d-flex flex-column gap-6">
        <x-card>
            <x-slot:header>Environment Setup (Read-only)</x-slot:header>
            <p>Inspect configuration readiness and local application requirements. These pages do not change settings or contact providers.</p>
            <div class="row g-4">
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <a class="btn btn-lg btn-outline btn-outline-primary w-100 h-100" href="{{ route('system.configuration-readiness') }}">
                        <x-icon.font-awesome class="fa-external-link" /> Configuration Readiness
                    </a>
                </div>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <a class="btn btn-lg btn-outline btn-outline-primary w-100 h-100" href="{{ route('system.diagnostics') }}">
                        <x-icon.font-awesome class="fa-external-link" /> System Diagnostics
                    </a>
                </div>
            </div>
        </x-card>
		{{-- start::CMS Links --}}
		<x-card>
			<x-slot:header>Content Management System</x-slot:header>
			<div class="d-flex flex-column gap-4">
				<x-callout class="bg-danger-subtle border-danger">
					<x-slot:icon>
						<x-icon.font-awesome class="fa-triangle-exclamation text-danger" />
					</x-slot:icon>
					<x-slot:title>
						<p class="text-danger fw-bold">Warning</p>
					</x-slot:title>
					<p class="text-danger">
						These links manipulate the data clients can choose from. If you know what you are doing, please proceed with
						precaution
					</p>
					<p class="text-muted small mb-0">
						This is intended to be used by the developers and maintainers of the system.
					</p>
				</x-callout>
				@include('system.index.partials.cms.links')
			</div>
		</x-card>
		{{-- end::CMS Links --}}

		{{-- start::System Links --}}
		<x-card>
			<x-slot:header>System Settings</x-slot:header>
			<div class="d-flex flex-column gap-4">
				<x-callout class="bg-danger-subtle border-danger">
					<x-slot:icon>
						<x-icon.font-awesome class="fa-triangle-exclamation text-danger" />
					</x-slot:icon>
					<x-slot:title>
						<p class="text-danger fw-bold">Warning</p>
					</x-slot:title>
					<p class="text-danger">
						These links manipulate the behavior and functions of the system, and users and their roles. Please procede with
						extreme precaution when accessing these links.
					</p>
					<p class="text-muted small mb-0">
						This is intended to be used by the developers and maintainers of the system.
					</p>
				</x-callout>
				@include('system.index.partials.settings.links')
			</div>
		</x-card>
		{{-- end::System Links --}}
	</div>
@endsection
