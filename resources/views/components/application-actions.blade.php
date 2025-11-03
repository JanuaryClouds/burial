@props([
    'application' => []
])
<div class="d-flex justify-content-start align-items-center">
    @if (auth()->user())
        @if ($application->assigned_to == auth()->user()->id || $application->assigned_to == null)
            <div class="btn-group dropdown">
                <a name="" id="" class="btn btn-primary" href="{{ route('admin.applications.manage', ['id' => $application->id]) }}" role="button">
                    View 
                </a>
                @if ($application->status == "pending" || $application->status == "approved" || $application->status == "processing")
                    <button id="action-options" class="btn btn-primary dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="sr-only">
                            <i class="fas fa-caret-down"></i>
                        </span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="action-options">
                        @can('add-updates')
                            <button class="dropdown-item" type="button" data-toggle="modal" data-target="#addUpdateModal-{{ $application->id }}">
                                Add Progress Update
                            </button>
                        @endcan
                        @if (!Request::is('reports/*'))
                            @can('assign')
                                <button class="dropdown-item" type="button" data-toggle="modal" data-target="#assign-modal-{{ $application->id }}">
                                    Assign Application
                                </button>
                            @endcan
                        @endif
                        @can('reject-applications')
                            <button class="dropdown-item" type="button" data-toggle="modal" data-target="#reject-{{ $application->id }}" title="Reject Application">
                                Reject Application
                            </button>
                        @endcan
                    </div>
                @elseif ($application->status == "rejected" && auth()->user()->can('reject-applications'))
                    <button class="btn btn-success" type="button" data-toggle="modal" data-target="#reject-{{ $application->id }}" title="Restore Application">
                        <i class="fas fa-rotate-left"></i>
                    </button>
                @endif
            </div>
        @else
            <button class="btn btn-link" onclick="showAssignModal()">
                <i class="fas fa-user-lock"></i>
            </button>

            <script>
                function showAssignModal() {
                    swal('Notice', "This application is assigned to someone else", "info" )
                }
            </script>
        @endif

        @can('assign')
            <div id="assign-modal-{{ $application->id }}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="my-modal-title" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="my-modal-title">
                                Assign Application to
                            </h5>
                            <button class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ route('admin.assignments.assign', ['id' => $application->id]) }}" method="POST" class="m-0 p-0">
                                @csrf
                                <select name="assigned_to" id="assigned_to" class="form-control" onchange="this.form.submit()">
                                    @if ($application->assigned_to)
                                        <option value="{{ $application->assignedTo->id }}">{{ $application->assignedTo->first_name }}</option>
                                    @endif
                                    <option value="">No Assignment</option>
                                    @foreach ($admins as $admin)
                                        @if ($admin->id !== $application->assigned_to)
                                            <option value="{{ $admin->id }}">{{ $admin->first_name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    @endif
</div>
