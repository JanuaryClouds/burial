@props([
    'application' => [],
])
{{-- Used in Datatables --}}
<div class="d-flex justify-content-start align-items-center">
    @if (auth()->user())
        @if ($application->status == 'pending' || $application->status == 'approved' || $application->status == 'processing')
            <button id="action-options" class="btn btn-light btn-sm dropdown-toggle dropdown-toggle-split"
                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Actions
                <span class="sr-only">
                    <i class="fas fa-caret-down"></i>
                </span>
            </button>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="action-options">
                <a name="" id="" class="dropdown-item"
                    href="{{ route('burial.show', ['id' => $application->id]) }}" role="button">
                    View
                </a>
                @can('add-updates')
                    <button class="dropdown-item" type="button" data-bs-toggle="modal"
                        data-bs-target="#addUpdateModal-{{ $application->id }}">
                        Add Progress Update
                    </button>
                @endcan
                @if (!Request::is('reports/*'))
                    @can('manage-assignments')
                        <button class="dropdown-item" type="button" data-bs-toggle="modal"
                            data-bs-target="#assign-modal-{{ $application->id }}">
                            Assign Application
                        </button>
                    @endcan
                @endif
                @can('reject-applications')
                    <button class="dropdown-item" type="button" data-bs-toggle="modal"
                        data-bs-target="#reject-{{ $application->id }}" title="Reject Application">
                        Reject Application
                    </button>
                @endcan
            </div>
        @elseif ($application->status == 'rejected' && auth()->user()->can('reject-applications'))
            <button class="btn btn-success" type="button" data-bs-toggle="modal"
                data-bs-target="#reject-{{ $application->id }}" title="Restore Application">
                <i class="fas fa-rotate-left"></i>
            </button>
        @endif
    @endif
</div>
