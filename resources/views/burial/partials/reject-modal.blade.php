<div id="reject-{{ $data->id }}" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <form action="{{ route('application.reject.toggle', ['id' => $data->id]) }}" method="post">
                @csrf
                <div class="modal-body">
                    <p>
                        Are you sure you want to {{ $data->status == 'rejected' ? 'restore' : 'reject' }}
                        {{ $data->beneficiary()?->fullname() ?? 'Unknown' }}&apos;s application
                        for burial
                        assistance?
                        {{ $data->status == 'rejected' ? 'This application will return to being processed.' : 'This application will not receive any further updates. The claimant will be notified via SMS about the rejection and reason.' }}
                    </p>
                    @if ($data->status != 'rejected')
                        <div class="form-group">
                            <label for="reason">Reason</label>
                            <textarea class="form-control" id="reason-{{ $data->id }}" name="reason" rows="3" required></textarea>
                        </div>
                    @endif
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" type="button" data-bs-dismiss="modal">
                        Cancel
                    </button>
                    <button
                        class="btn {{ $data->status != 'rejected' ? 'bg-danger text-white' : 'bg-success text-white' }}"
                        type="submit">
                        {{ $data->status == 'rejected' ? 'Restore Application' : 'Reject Application' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
