<div class="bg-body shadow-xs p-4 rounded">
    <div class="d-flex justify-content-between gap-3">
        <span class="d-flex align-items-center gap-2">
            <a href="{{ route('clients.gis-form', ['id' => $data->originalClaimant()?->client->id]) }}"
                class="btn btn-light mr-2" data-no-loader>
                Generate GIS Form
            </a>
            <a href="{{ route('burial.certificate', ['id' => $data->id]) }}" class="btn btn-light mr-2" target="_blank">
                Download Certificate
            </a>
            @if (app()->hasDebugModeEnabled() && $data->tracker)
                <a href="{{ route('tracker.show') }}" class="btn btn-warning mr-2" target="_blank">
                    <i class="fas fa-eye"></i>
                    View as Guest
                </a>
            @endif
        </span>
        <span class="d-flex gap-3 align-items-center">
            @if ($data->status != 'rejected' && $data->status != 'released' && $next_step != null)
                <button class="btn btn-primary mr-2" type="button" data-bs-toggle="modal"
                    data-bs-target="#addUpdateModal-{{ $data->id }}">
                    Add Progress Update
                </button>
                @can('reject-applications')
                    <button class="btn btn-danger" type="button" data-bs-toggle="modal"
                        data-bs-target="#reject-{{ $data->id }}">
                        Reject Application
                    </button>
                @endcan
            @endif
            @if ($data->status == 'rejected')
                @can('reject-applications')
                    <button class="btn btn-success" type="button" data-bs-toggle="modal"
                        data-bs-target="#reject-{{ $data->id }}">
                        Restore Application
                    </button>
                @endcan
            @endif
        </span>
    </div>
</div>
