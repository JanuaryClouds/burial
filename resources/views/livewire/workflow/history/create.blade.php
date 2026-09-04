<div>
	@if ($showForm)
		<x-card>
			<div class="row">
				<div class="col-4">
					<x-button wire:click="setDateInToNow"
						wire:loading.remove
						class="btn-primary">
						Date In
					</x-button>
				</div>
				<div class="col-8">
					<x-form.input wire:model.live='dateIn'
						name="dateIn"
						required
						readonly
						type="datetime-local"
						step="1" />
				</div>
			</div>
			@if ($dateIn)
				<div class="separator separator-dashed my-4"></div>
				{{-- Extra fields --}}

				{{-- Choose Stage to go to --}}
				<x-form.select :options="$stages->pluck('name', 'uuid')"
					wire:model.live='toStageUuid'
					wire:loading.remove
					name="toStageUuid"
					label="Target Stage"
					required />
				@if ($toStageUuid && $toStageUuid !== $application->toStage()->uuid)
					<x-form.textarea wire:model='reason'
						wire:loading.remove
						required
						name="reason"
						label="Reason" />
				@endif
			@endif
			@if ($toStageUuid)
				<div class="separator separator-dashed my-4"></div>
				<div class="row">
					<div class="col-4">
						<x-button wire:click="setDateOutToNow"
							wire:loading.remove
							class="btn-primary">
							Date Out
						</x-button>
					</div>
					<div class="col-8">
						<x-form.input wire:model.live='dateOut'
							name="dateOut"
							readonly
							type="datetime-local"
							step="1" />
					</div>
				</div>
			@endif
			{{-- Remarks --}}
			<x-slot:footer>
				<x-button wire:click="submit"
					wire:loading.remove
					class="btn-sm btn-success">
					<x-icon.font-awesome icon="floppy-disk" />
					Save
				</x-button>
			</x-slot:footer>
		</x-card>
	@else
		<x-card.unauthorized>
			<x-slot:header>Unauthorized</x-slot:header>
		</x-card.unauthorized>
	@endif
</div>
