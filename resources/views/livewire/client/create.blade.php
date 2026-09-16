<x-slot:page_title>Create Client</x-slot:page_title>
<x-slot:page_subtitle>Funeral Assistance System | CSWDO Taguig</x-slot:page_subtitle>
<div class="d-flex flex-column gap-4">
	@include('client.create.partials.documents')
	<x-card>
		<div class="d-flex flex-column gap-4">
			{{-- start::Warnings --}}
			@include('client.create.partials.warnings')
			{{-- end::Warnings --}}

			{{-- start::Basic Information --}}
			@include('client.create.partials.basic-information')
			{{-- end::Basic Information --}}

			<div class="separator separator-dashed my-4"></div>

			{{-- start::Address --}}
			@include('client.create.partials.address')
			{{-- end::Address --}}

			<div class="separator separator-dashed my-4"></div>

			{{-- start::Social Information --}}
			@include('client.create.partials.social-information')
			{{-- end::Social Information --}}
		</div>
		<x-slot:footer>
			<a href="{{ route('client.index') }}"
				class="btn btn-sm btn-light"
				role="button">
				<x-icon.font-awesome class="fa-xmark" />
				Cancel
			</a>
			<x-button class="btn-sm btn-success"
				wire:click="save"
				wire:loading.attr="disabled">
				<x-icon.font-awesome class="fa-floppy-disk" />
				<span wire:loading.remove>
					Save as Draft
				</span>
				<span wire:loading>
					Saving...
				</span>
			</x-button>
		</x-slot:footer>
	</x-card>
</div>
