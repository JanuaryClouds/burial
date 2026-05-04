<div class="modal fade" id="referralModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false" role="dialog"
    aria-labelledby="referralForm" aria-hidden="true">
    <form action="{{ route('clients.referral.store', ['id' => $client->id]) }}" method="post">
        @csrf
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="referralForm">
                        Refer to Other Department
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @include('components.form-input', [
                        'name' => 'referral_to',
                        'label' => 'Referral To',
                        'type' => 'text',
                        'required' => true,
                    ])
                    @include('components.form-textarea', [
                        'name' => 'remarks',
                        'label' => 'Remarks',
                        'required' => true,
                    ])
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">Submit Referral</button>
                </div>
            </div>
        </div>
    </form>
</div>
