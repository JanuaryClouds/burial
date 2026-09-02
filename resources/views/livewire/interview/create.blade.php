<div>
	<x-card>
		@if ($client->interviews->where('schedule', '>', now())->count() === 0)
			<x-form.input name="schedule"
				label="Schedule"
				type="datetime-local"
				wire:model="schedule" />
			<x-slot:footer>
				<x-button type="button"
					wire:click="save"
					wire:loading.attr='disabled'
					class="btn btn-primary">
					<i class="fa-solid fa-floppy-disk"
						wire:loading.remove></i>
					<span wire:loading>Saving...</span>
					<span wire:loading.remove>Save</span>
				</x-button>
			</x-slot:footer>
		@else
			<div class="d-flex flex-center">
				<span>Interview already scheduled.</span>
			</div>
		@endif
	</x-card>
</div>
