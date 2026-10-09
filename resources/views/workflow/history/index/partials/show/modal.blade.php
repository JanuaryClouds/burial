<div wire:ignore.self
	class="modal fade"
	id="showHistoryDetailsModal"
	tabindex="-1"
	data-bs-backdrop="static"
	data-bs-keyboard="false"
	role="dialog"
	aria-labelledby="HistoryDetailsModalTitle"
	aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-xl"
		role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title"
					id="HistoryDetails">
					Workflow History Log Details
				</h5>
				<button type="button"
					class="btn-close"
					data-bs-dismiss="modal"
					wire:click="clearSelectedHistory"
					aria-label="Close"></button>
			</div>
			<div class="modal-body">
				{{-- start::Workflow History Details --}}
				<livewire:workflow.history.show :selectedHistory="$selectedHistory" />
				{{-- end::Workflow History Details --}}
			</div>
			<div class="modal-footer">
				<button type="button"
					class="btn btn-sm btn-secondary"
					wire:click="clearSelectedHistory"
					data-bs-dismiss="modal">
					Close
				</button>
			</div>
		</div>
	</div>
</div>
