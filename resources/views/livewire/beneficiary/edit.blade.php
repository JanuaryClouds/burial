<x-card>
	<x-slot:header>Beneficiary's Information</x-slot:header>
	<div class="d-flex flex-column gap-4">
		{{-- start::Basic Information --}}
		@include('beneficiary.partials.basic-information')
		{{-- end::Basic Information --}}

		<div class="separator separator-dashed my-4"></div>

		{{-- start::Social Information --}}
		@include('beneficiary.partials.social-information')
		{{-- end::Social Information --}}

		<div class="separator separator-dashed my-4"></div>

		{{-- start::Address --}}
		@include('beneficiary.partials.address')
		{{-- end::Address --}}
	</div>
	<x-slot:footer>
		<a href="{{ route('beneficiary.show', $beneficiary) }}"
			class="btn btn-sm btn-light">
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
