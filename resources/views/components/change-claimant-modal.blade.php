<!-- Modal trigger button -->
<button
    type="button"
    class="btn btn-primary text-capitalize"
    data-bs-toggle="modal"
    data-bs-target="#changeClaimantModal"
>
    change Claimant
</button>

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
        class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-lg"
        role="document"
    >
        <form action="">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Change Claimant
                    </h5>
                    <button
                        type="button"
                        class="btn-close"
                        data-bs-dismiss="modal"
                        aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to change the claimant? This can only be done once.</p>
                    <p>Note: Changing the claimant will lengthen the processing of this application</p>
                    <x-claimant-form />
                </div>
                <div class="modal-footer">
                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal"
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
