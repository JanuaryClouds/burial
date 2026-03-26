<div class="card">
    <div class="card-header">
        <h4 class="card-title">Request for Claimant Change</h4>
    </div>
    <div class="card-body">
        <p>A change of claimants have been requested for this application. Please contact the original
            claimant for confirmation.</p>
        <x-form-input name="reason_for_change" id="reason_for_change" label="Reason for Change" placeholder=""
            value="{{ $data->claimantChanges()?->first()?->reason_for_change }}" readonly="true" disabled="true" />
        <x-claimant-form :claimant="$data->claimantChanges()?->first()?->newClaimant" :readonly="true" :disabled="true" />
        <form
            action="{{ route('burial.claimant-change.decision', [
                'id' => $data->id,
                'change' => $data->claimantChanges()?->first()?->id,
            ]) }}"
            method="post">
            @csrf
            <div class="d-flex justify-content-end gap-2 mt-2 align-items-center">
                <x-form-select name="decision" id="decision" :options="['approved' => 'Approve', 'rejected' => 'Reject']" />
                <div class="mb-3">
                    <button class="btn btn-success ml-2" id="submitDecision" type="submit" disabled>Submit
                        Decision</button>
                </div>
            </div>
        </form>
    </div>
</div>
<script nonce={{ $nonce ?? '' }} }}>
    const decision = document.getElementById('decision');
    const decisionBtn = document.getElementById('submitDecision');

    decision.addEventListener('change', () => {
        if (decision.value != 'Select one') {
            decisionBtn.removeAttribute('disabled');
        } else {
            decisionBtn.setAttribute('disabled', 'true');
        }
    });
</script>
