<div>
	<x-card>
		<x-slot:header>Recommendation</x-slot:header>
		<x-form.select name="funeralAssistanceTypeUuid"
			wire:model='funeralAssistanceTypeUuid'
			:options="$funeralAssistanceTypes"
			label="Funeral Assistance Type"
			required />
		<x-form.input name="amountExtended"
			wire:model='amountExtended'
			label="Amount to Extend"
			required
			type="number" />
		<x-form.select name="modeOfAssistanceId"
			wire:model='modeOfAssistanceId'
			:options="$modeOfAssistances"
			label="Mode of Assistance"
			required />
		<x-slot:footer>
			<x-button wire:click='save'
				wire:loading.attr='disabled'
				class="btn-sm btn-success">
				<i class="fa-solid fa-floppy-disk"></i>
				<span wire:loading.remove>Save</span>
				<span wire:loading>Saving...</span>
			</x-button>
		</x-slot:footer>
	</x-card>
</div>
