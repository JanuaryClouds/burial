<div id="assessment-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="assessment-modal" aria-hidden="true">
    <form action="{{ route('clients.assessment.store', ['id' => $client->id]) }}" method="post">
        @csrf
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="assessment-modal">{{ $client->first_name . ' ' . $client->last_name }}'s Assessment</h5>
                    <button class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    @include('client.partial.beneficiary-assessment')
                </div>
                <div class="modal-footer">
                    <button class="btn btn-success" type="submit">
                        <i class="fas fa-check-circle"></i>
                        Submit
                    </button>
                    <button type="button" class="btn btn-warning" data-dismiss="modal">
                        <i class="fas fa-times-circle"></i>
                        Close
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>