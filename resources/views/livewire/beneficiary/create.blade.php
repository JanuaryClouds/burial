<x-slot:page_title>Create New Beneficiary</x-slot:page_title>
<x-slot:page_subtitle>Funeral Assistance System | CSWDO Taguig</x-slot:page_subtitle>

<div class="d-flex flex-column gap-4">
	{{-- start::Warnings --}}
	@include('beneficiary.create.partials.warnings')
	{{-- end::Warnings --}}

	{{-- start::Beneficiary Information --}}
	<x-card>
		<x-slot:header>Beneficiary's Information</x-slot:header>
		<div class="d-flex flex-column gap-4">
			{{-- start::Basic Information --}}
			@include('beneficiary.create.partials.basic-information')
			{{-- end::Basic Information --}}

			<div class="separator separator-dashed my-4"></div>

			{{-- start::Social Information --}}
			@include('beneficiary.create.partials.social-information')
			{{-- end::Social Information --}}

			<div class="separator separator-dashed my-4"></div>

			{{-- start::Address --}}
			@include('beneficiary.create.partials.address')
			{{-- end::Address --}}
		</div>
	</x-card>
	{{-- end::Beneficiary Information --}}

	{{-- Family Composition --}}
	@include('beneficiary.family.partials.create')

	<x-card>
		<p class="">
			After successfully submitting the form, you will be redirected to the application builder where you can choose
			drafted Client records and drafted Beneficiary records. Please make sure to prepare the documents stated before
			submitting a client record, for uploading into the system.
		</p>
		<x-slot:footer>
			<a href="{{ route('beneficiary.index') }}"
				class="btn btn-light btn-sm">
				<x-icon.font-awesome class="fa-xmark" />
				Cancel
			</a>
			<x-button wire:click="save"
				wire:loading.attr='disabled'
				class="btn-sm btn-success">
				<x-icon.font-awesome class="fa-floppy-disk" />
				<span wire:loading.remove>Save As Draft</span>
				<span wire:loading>Saving...</span>
			</x-button>
		</x-slot:footer>
	</x-card>
</div>
