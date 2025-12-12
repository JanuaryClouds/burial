@can('delete-resource', $entry)
    <!-- Delete content modal -->
    <div id="delete-modal-{{ $entry->id }}" class="modal fade" tabindex="-1" role="dialog"
        aria-labelledby="delete-modal-{{ $entry->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <form action="{{ route('cms.delete', ['type' => $type, 'id' => $entry->id]) }}" method="post">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="delete-modal-{{ $entry->id }}-title">Delete {{ $entry->name }}</h5>
                        <button class="btn btn-icon btn-sm btn-active-icon-primary" type="button" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="ki-duotone ki-cross fs-1">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete {{ $entry->name }} in the database? This will affect data
                            connected to this.</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-light" data-bs-dismiss="modal" aria-label="Close">
                            Cancel
                        </button>
                        <button class="btn btn-danger" type="submit">
                            Confirm Deletion
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endcan
