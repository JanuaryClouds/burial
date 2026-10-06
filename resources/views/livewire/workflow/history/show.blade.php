<div wire:ignore
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
					aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<div class="row">
					{{-- start::Extra fields --}}
					<div class="col-12 col-xl-6">

					</div>
					{{-- end::Extra fields --}}

					{{-- start::Remarks --}}
					<div class="col-12 col-xl-6">
						<livewire:remark.index defer />
					</div>
					{{-- end::Remarks --}}
				</div>
			</div>
			<div class="modal-footer">
				<button type="button"
					class="btn btn-secondary"
					data-bs-dismiss="modal">
					Close
				</button>
			</div>
		</div>
	</div>
</div>
