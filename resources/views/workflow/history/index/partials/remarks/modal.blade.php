<div wire:ignore.self
	class="modal fade"
	id="showRemarksModal"
	tabindex="-1"
	data-bs-backdrop="static"
	data-bs-keyboard="false"
	role="dialog"
	aria-labelledby="RemarksModal"
	aria-hidden="true">
	<div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-xl"
		role="document">
		<div class="modal-content">
			<div class="modal-body">
				{{-- start::Remarks --}}
				<livewire:remark.index defer />
				{{-- end::Remarks --}}
			</div>
			<div class="modal-footer">
				<button type="button"
					class="btn btn-sm btn-secondary"
					data-bs-dismiss="modal">
					Close
				</button>
			</div>
		</div>
	</div>
</div>
