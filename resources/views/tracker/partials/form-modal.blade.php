<div class="modal fade" id="trackAssistanceModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="true"
    role="dialog" aria-labelledby="trackAssistanceForm" aria-hidden="true">
    <form action="{{ route('tracker.match') }}" method="post">
        @csrf
        <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="trackAssistanceForm">
                        Track Assistance
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Provide the client tracking number and assistance tracking number.</p>
                    <x-form-input name="tracking_no" label="Client Tracking Number"
                        helpText="Tracking numbers are provided once clients have submitted an application."
                        required="true" placeholder="{{ now()->format('Y') . '-XXXX' }}" />
                    <x-form-input name="code" label="Assistance Tracking Code"
                        helpText="Created when the original client of the assistance issued a tracking code. Please contact the original client of the desired assistance for the tracking code."
                        required="true" placeholder="{{ 'ABC-123' }}" />
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-primary">Track</button>
                </div>
            </div>
        </div>
    </form>
</div>
