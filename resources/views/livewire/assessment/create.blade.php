<div>
	<x-card>
		<x-slot:header>Assessment</x-slot:header>
		<x-form.textarea name="problem_presented"
			wire:model="problem_presented"
			label="Problem Presented"
			required />
		<x-form.textarea name="swa"
			wire:model="swa"
			label="Social Worker's Assessment"
			required />
		<x-slot:footer>
			<x-button wire:click='save'
				class="btn-sm btn-success">
				<i class="fa-solid fa-floppy-disk"></i>
				<span wire:loading.remove>Save</span>
				<span wire:loading>Saving</span>
			</x-button>
		</x-slot:footer>
	</x-card>
</div>
