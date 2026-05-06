<div class="card">
    <form action="{{ route('burial.claimant-change.store', ['id' => $data->id]) }}" method="post" id="changeClaimantForm">
        @csrf
        <div class="card-header">
            <h4 class="card-title">Change Claimant of Assistance</h4>
        </div>
        <div class="card-body">
            <p class="card-text">
                If the current claimant is unable to receive the assistance, you can change the claimant of the
                assistance by providing
                their information below.
            </p>
            <div class="alert alert-danger" role="alert">
                <strong>Note:</strong>
                Only clients with application history can be assigned as the new claimant.
            </div>
            <div class="alert alert-danger" role="alert">
                <strong>Note:</strong>
                Changing claimant can only be done once.
            </div>
            @include('burial.partials.claimant-form', [
                'readonly' => false,
                'claimant_change' => true,
            ])
            <x-form-textarea name="reason_for_change" :required="true" label="Reason for Changing Claimant" />
        </div>
        <div class="card-footer d-flex justify-content-end">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                data-bs-target="#confirmClaimantChangeModal">
                Submit Request
            </button>
        </div>
    </form>
</div>

<div class="modal fade" id="confirmClaimantChangeModal" tabindex="-1" data-bs-backdrop="static"
    data-bs-keyboard="false" role="dialog" aria-labelledby="confirmClaimantChangeAlert" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmClaimantChangeAlert">
                    Confirm Submitted Information
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="card-text fs-3">Are you sure you want to change claimants of the assistance?
                    You can only request a change of claimant once.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="submit" class="btn btn-primary" id="confirmClaimantChangeBtn">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script nonce="{{ $nonce ?? '' }}">
    $('#confirmClaimantChangeBtn').on('click', function() {
        $(this).prop('disabled', true);
        $(this).text('Please wait...');
        triggerLoading(0.3);
        $('#changeClaimantForm').submit();
    });
</script>
