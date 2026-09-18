<x-slot:pageTitle>Edit Client</x-slot:pageTitle>
<x-slot:pageSubTitle>Funeral Assistance System | CSWDO Taguig</x-slot:pageSubTitle>

<x-card>
	<div class="d-flex flex-column gap-4">
		{{-- start::Basic Information --}}
		@include('client.partials.basic-information')
		{{-- end::Basic Information --}}

		<div class="separator separator-dashed my-4"></div>

		{{-- start::Address --}}
		@include('client.partials.address')
		{{-- end::Address --}}

		<div class="separator separator-dashed my-4"></div>

		{{-- start::Social Information --}}
		@include('client.partials.social-information')
		{{-- end::Social Information --}}
	</div>
	<x-slot:footer>
		<a href="{{ route('client.show', $client) }}"
			class="btn btn-sm btn-light"
			role="button">
			<x-icon.font-awesome class="fa-xmark" />
			Cancel
		</a>
		<x-button class="btn-sm btn-success"
			wire:click="save"
			wire:dirty
			wire:loading.attr="disabled">
			<x-icon.font-awesome class="fa-floppy-disk" />
			Update as Draft
		</x-button>
	</x-slot:footer>
</x-card>
