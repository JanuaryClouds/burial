@php
	$documents = App\Models\DocumentRequirement::burial();
@endphp
<div class="row">
	@foreach ($documents as $document)
		@php
			$displayName = $document['name'];

			if ($document['is_muslim']) {
			    $displayName .= ' (For Muslim Citizen Only)';
			}

			$model = "images.{$document['key']}";
			$image = data_get($images, $document['key']);

			$preview =
			    $image instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile ? $image->temporaryUrl() : null;
		@endphp

		<div class="col-12 col-md-6 d-flex flex-column gap-4 {{ $document['is_muslim'] ? 'muslim-requirements' : '' }}">
			<x-form.image :wire:model="$model"
				:label="$displayName" />
			<div
				class="d-flex justify-content-center w-100 h-100 px-4 py-3 border-2 border-dashed border-gray-200 rounded bg-gray-100"
				style="max-height: 300px;">
				@if ($preview)
					<img src="{{ $preview }}"
						alt="{{ $displayName }}"
						class="img-thumbnail object-fit-contain"
						style="max-width: 100%; max-height: 300px;" />
				@else
					<p class="fs-6 text-muted text-uppercase mb-0 fw-semibold align-self-center"
						wire:target="{{ $model }}"
						wire:loading.remove>Preview Here</p>
					<p class="fs-6 text-muted text-uppercase mb-0 fw-semibold align-self-center"
						wire:target="{{ $model }}"
						wire:loading>Uploading...</p>
				@endif
			</div>
			@if ($preview)
				<span class="align-self-center">
					<x-button wire:click="removeImage('{{ $document['key'] }}')"
						wire:loading.attr="disabled"
						class="btn-sm btn-danger">
						<x-icon.font-awesome class="fa-trash" />
						<span wire:loading.remove
							wire:target="{{ $model }}">Remove</span>
						<span wire:loading
							wire:target="{{ $model }}">Removing...</span>
					</x-button>
				</span>
			@endif
			<div class="separator separator-dashed my-4"></div>
		</div>
	@endforeach
</div>
@script
	<script nonce="{{ $nonce ?? '' }}">
		$(document).ready(function() {
			const beneficiaryReligion = $('#beneficiary_religion').find('option:selected').text();

			if (beneficiaryReligion === 'Muslim') {
				$('.muslim-requirements').show();
			} else {
				$('.muslim-requirements').hide();
			}
		});
	</script>
@endscript
