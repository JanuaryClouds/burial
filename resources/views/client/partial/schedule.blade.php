<div id="set-schedule-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="set-schedule-modal" aria-hidden="true">
    <form action="{{ route('clients.interview.schedule.store', ['id' => $client->id]) }}" method="post">
        @csrf
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="set-schedule-modal">Schedule for an interview</h5>
                    <button class="btn btn-icon btn-sm btn-active-icon-primary" data-bs-dismiss="modal" aria-label="Close">
                        <i class="ki-duotone ki-cross fs-1">
                            <span class="path1"></span><span class="path2"></span>
                        </i>
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
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        Close
                    </button>
                    <button type="submit" class="btn btn-primary">
                        Set Schedule
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>