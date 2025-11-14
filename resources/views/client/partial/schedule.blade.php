<div id="set-schedule-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="set-schedule-modal" aria-hidden="true">
    <form action="{{ route('clients.interview.schedule.store', ['id' => $client->id]) }}" method="post">
        @csrf
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="set-schedule-modal">Schedule for an interview</h5>
                    <button class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <x-form-input 
                        name="schedule"
                        label="Schedule"
                        type="datetime-local"
                        min="{{ Carbon\Carbon::now()->format('Y-m-d\TH:i') }}"
                        required
                    />
                    <x-form-textarea
                        name="remarks"
                        label="Remarks"
                    />
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check"></i>
                        Set Schedule
                    </button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times-circle"></i>
                        Close
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>