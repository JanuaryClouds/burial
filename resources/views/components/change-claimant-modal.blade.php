@props(['burialAssistance'])
<!-- Modal Body -->
<!-- if you want to close by clicking outside the modal, delete the last endpoint:data-bs-backdrop and data-bs-keyboard -->
<div
    class="modal fade"
    id="changeClaimantModal"
    tabindex="-1"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
    
    role="dialog"
    aria-labelledby="modalTitleId"
    aria-hidden="true"
>
    <div
        class="modal-dialog modal-dialog-scrollable modal-lg"
        role="document"
    >
        <form action="{{ route('guest.burial-assistance.claimant-change', ['id' => $burialAssistance->id]) }}" method="post">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Change Claimant
                    </h5>
                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal"
                        aria-label="Close"
                    ><i class="fa-solid fa-xmark"></i></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to change the claimant? This can only be done once.</p>
                    <p>Note: Changing the claimant will lengthen the processing of this application</p>
                    <x-claimant-form />
                    <div class="my-3 bg-white p-4 rounded shadow-sm">
                        <x-form-input 
                            name="reason_for_change"
                            id="reason_for_change"
                            label="Reason for Change"
                            placeholder=""
                            value=""
                            required="true"
                        />
                    </div>
                    <x-burial-assistance-image-requirements />
                </div>
                <div class="modal-footer">
                    <button
                        type="close"
                        class="btn btn-secondary"
                        data-dismiss="modal"
                    >
                        Close
                    </button>
                    <button type="submit" class="btn btn-primary">Submit Change Request</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Optional: Place to the bottom of scripts -->
<script>
    const myModal = new bootstrap.Modal(
        document.getElementById("modalId"),
        options,
    );
</script>
