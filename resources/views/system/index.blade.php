@extends('layouts.app')
@section('content')
	<div class="d-flex flex-column gap-6">
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
