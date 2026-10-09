<div class="d-flex flex-column gap-4">
	@if ($enableSubmission)
		<x-form.textarea wire:model.live="form.content"
			name="form.content"
			label="Remarks" />
		<div class="d-flex justify-content-end">
			<x-button wire:click='save'
				wire:loading.attr='disabled'
				class="btn-sm btn-primary">
				<x-icon.font-awesome class="fa-paper-plane" />
				Submit
			</x-button>
		</div>
	@endif
</div>
