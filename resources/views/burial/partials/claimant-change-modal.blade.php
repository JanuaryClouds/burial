<button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#changeClaimantsModal">
    Change Claimants
</button>

<div class="modal fade" id="changeClaimantsModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
    role="dialog" aria-labelledby="checkClaimantForm" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm" role="document">
        <form action="{{ route('burial.claimant-change.store', ['id' => $burialAssistance->id]) }}" method="post">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="checkClaimantForm">
                        Change Check Claimant
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">New Claimant Information</h4>
                        </div>
                        <div class="card-body">
                            @include('client.partial.client-info')
                        </div>
                    </div>
                    {{-- ! Skipped document submission. Needs confirmation from client before implementation --}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal"
                        data-bs-target="#confirmClaimantChangeModal">
                        Submit Request
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="confirmClaimantChangeModal" tabindex="-1" data-bs-backdrop="static"
    data-bs-keyboard="false" role="dialog" aria-labelledby="confirmClaimantChangeAlert" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmClaimantChangeAlert">
                    Confirm Submitted Information
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="card-text">Are you sure you want to change claimants of the assistance?
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
        document.getElementById('changeClaimantsModal').querySelector('form').submit();
    });
</script>
