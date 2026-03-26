<div class="modal fade" id="showTrackingCodeModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
    role="dialog" aria-labelledby="TrackingCodeModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="TrackingCodeModal">
                    Warning
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="modal-text">
                    You are about to display this assistance's tracking code. The tracking code acts as a password to
                    this assistance. Please ensure that you are in a private area to view this.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    Cancel
                </button>
                <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal"
                    data-bs-target="#confirmShowTrackingCodeModal">
                    Confirm
                </button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="confirmShowTrackingCodeModal" tabindex="-1" data-bs-backdrop="static"
    data-bs-keyboard="false" role="dialog" aria-labelledby="confirmedTrackingCodeModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmedTrackingCodeModal">
                    Assistance Tracking Code
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <button type="button" class="btn btn-secondary" data-bs-toggle="tooltip" data-bs-placement="top"
                    title="Copy to clipboard" id="copyCodeButton">
                    {{ $data->tracker?->code ?? 'No Tracking Code' }}
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
<script nonce="{{ $nonce ?? '' }}">
    document.getElementById('copyCodeButton').addEventListener('click', function() {
        const button = this;
        const originalTitle = button.getAttribute('data-bs-original-title') || button.getAttribute('title');
        navigator.clipboard.writeText(this.innerText.trim())
            .then(function() {
                button.setAttribute('data-bs-original-title', 'Copied!');
                const tooltip = bootstrap.Tooltip.getInstance(button);
                if (tooltip) tooltip.show();
                setTimeout(function() {
                    button.setAttribute('data-bs-original-title', originalTitle);
                }, 1500);
            })
            .catch(function(err) {
                console.error('Failed to copy: ', err);
            });
    });
</script>
