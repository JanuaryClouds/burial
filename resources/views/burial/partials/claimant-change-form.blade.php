<div class="card">
    <form action="{{ route('burial.claimant-change.store', ['id' => $data->id]) }}" method="post" id="changeClaimantForm">
        @csrf
        <div class="card-header">
            <h4 class="card-title">Change Claimants of Assistance</h4>
        </div>
        <div class="card-body">
            <p class="card-text">
                If you are unable to receive the assistance, you can change the claimants of the assistance by providing
                their information
            </p>
            <div class="alert alert-danger" role="alert">
                <strong>Note:</strong>
                Changing claimants will still register your account as the client. To allow the new claimant to track
                this
                assistance. Ensure tracker sharing is enabled.
            </div>
            <div class="alert alert-danger" role="alert">
                <strong>Note:</strong>
                Changing claimants can only be done once.
            </div>
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">New Claimant Information</h4>
                </div>
                <div class="card-body">
                    @include('client.partials.client-info')
                    <x-form-textarea name="reason_for_change" required="true" label="Reason for Changing Claimants" />
                </div>
            </div>
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
                    You can only request a change of claimants once. Providing insufficient or
                    incorrect information will lead to rejection of this request. Please double
                    check your provided information regarding the new claimant before confirming.
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
    document.getElementById('confirmClaimantChangeBtn').addEventListener('click', function() {
        this.disabled = true;
        this.textContent = 'Submitting...';
        document.getElementById('changeClaimantForm').submit();
    });
</script>
