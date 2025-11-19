<div id="assessment-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="assessment-modal" aria-hidden="true">
    <form action="{{ route('clients.assessment.store', ['id' => $client->id]) }}" method="post">
        @csrf
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assessment-modal">{{ $client->first_name . ' ' . $client->last_name }}'s Assessment</h5>
                    <button class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span><span class="path2"></span>
                        </i>
                    </button>
                </div>
                <div class="modal-body">
                    @include('client.partial.beneficiary-assessment')
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button class="btn btn-success" type="submit">
                        Submit
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>