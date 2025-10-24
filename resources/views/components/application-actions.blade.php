@props([
    'application' => []
])
<div class="d-flex justify-content-start align-items-center">
    @if (auth()->user())
        @if (auth()->user()->isAdmin())
            @if (!$application->assigned_to || $application->assigned_to == auth()->user()->id)
                <a name="" id="" class="btn btn-primary" href="{{ route('admin.applications.manage', ['id' => $application->id]) }}" role="button">
                    <i class="fas fa-external-link-square-alt"></i>  
                </a>
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
        @elseif (auth()->user()->hasRole('superadmin'))
            <div class="d-flex">
                @if ($application->status != "released" || $application->status != "rejected")
                    @if ($application->assignedTo == null)
                        <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#assign-modal-{{ $application->id }}">
                            <i class="fas fa-user-check"></i>
                        </button>
                    @else
                        <button class="btn btn-light" type="button" data-toggle="modal" data-target="#assign-modal-{{ $application->id }}">
                            <i class="fas fa-user-check"></i>
                        </button>
                    @endif
                @endif
                <span class="mr-2"></span>
                @if ($application->status != "rejected" || $application->status != "released")
                    <button class="btn btn-danger" type="button" data-toggle="modal" data-target="#reject-{{ $application->id }}">
                        <i class="fas fa-times-circle"></i>
                    </button>
                @else
                    <button class="btn btn-warning" type="button" data-toggle="modal" data-target="#reject-{{ $application->id }}">
                        <i class="fas fa-rotate-left"></i>
                    </button>
                @endif
            </div>

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
                            <form action="{{ route('superadmin.assignments.assign', ['id' => $application->id]) }}" method="POST" class="m-0 p-0">
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

            <div id="reject-{{ $application->id }}" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <form action="{{ route('superadmin.assignments.reject.toggle', ['id' => $application->id]) }}" method="post">
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
        @endif
    @endif
</div>
