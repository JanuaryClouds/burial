@props(['application'])
<div id="reject-{{ $application->id }}" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('assignments.reject.toggle', ['id' => $application->id]) }}" method="post">
                @csrf
                <div class="modal-body">
                    <p>
                        Are you sure you want to {{ $application->status == "rejected" ? "unreject" : "reject" }} this application?
                        {{ $application->status == "rejected" ? "This application will return to being processed." : "This application will not receive any further updates." }}
                    </p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-primary" type="submit">
                        <i class="fas fa-floppy-disk"></i>
                        {{ $application->status == "rejected" ? "Unreject" : "Reject" }}
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