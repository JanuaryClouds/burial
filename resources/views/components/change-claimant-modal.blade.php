<!-- Modal trigger button -->
<button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#changeClaimantModal">
    Change Claimant
</button>

<!-- Modal Body -->
<!-- if you want to close by clicking outside the modal, delete the last endpoint:data-bs-backdrop and data-bs-keyboard -->
<div class="modal fade" id="changeClaimantModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
    role="dialog" aria-labelledby="changeClaimantModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeClaimantModal">
                    Change Claimant of Burial Assistance
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body fs-4">
                <p>
                    Are you sure you want to change the claimant of this burial
                    assistance?
                </p>
                <p>Send the link below to the new claimant. Please ask the new claimant to be registered in the Taguig
                    Citizen's Portal in-order for them to receive the assistance.</p>
                <button id="changeClaimantLink" class="btn btn-primary"
                    data-link="{{ route('burial.claimant-change', ['uuid' => $burialAssistance->id]) }}">
                    Copy link to Clipboard
                </button>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    const changeClaimantLink = document.getElementById('changeClaimantLink');

    changeClaimantLink.addEventListener('click', function() {

        const linkToCopy = changeClaimantLink.dataset.link; // <-- recommended (see below)

        navigator.clipboard.writeText(linkToCopy)
            .then(() => {
                Swal.fire({
                    icon: 'success',
                    title: 'Link copied!',
                    text: 'You can now send the link to the new claimant.',
                    timer: 3000,
                    showConfirmButton: false
                });
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Failed to copy',
                    text: 'Something went wrong while copying the link.'
                });
            });
    });
</script>
