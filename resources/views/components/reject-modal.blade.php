@props(['application'])
<div id="reject-{{ $application->id }}" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('assignments.reject.toggle', ['id' => $application->id]) }}" method="post">
                @csrf
                <div class="modal-body">
                    <p>
                        Are you sure you want to {{ $application->status == "rejected" ? "restore" : "reject" }} {{ $application->deceased->first_name }} {{ $application->deceased->last_name }}&apos;s application?
                        {{ $application->status == "rejected" ? "This application will return to being processed." : "This application will not receive any further updates. The claimant will be notified via SMS about the rejection and reason" }}
                    </p>
                    @if ($application->status != 'rejected')
                        <div class="form-group">
                            <label for="reason">Reason</label>
                            <textarea class="form-control" id="reason" name="reason" rows="3" required></textarea>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-rotate-left"></i>
                        {{ $application->status == "rejected" ? "Restore Application" : "Reject Application" }}
                    </button>
                    <button class="btn btn-secondary" type="button" data-dismiss="modal">
                        <i class="fas fa-times-circle"></i>
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>