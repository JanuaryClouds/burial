@props([
    'application' => []
])
<div class="d-flex justify-content-start align-items-center">
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
    @else
        <form action="{{ route('superadmin.assignments.assign', ['id' => $application->id]) }}" method="POST" class="m-0 p-0">
            @csrf
            <select name="assigned_to" id="assigned_to" class="form-control" onchange="this.form.submit()">
                @if ($application->assigned_to)
                    <option value="{{ $application->assignedTo->id }}">{{ $application->assignedTo->first_name }}</option>
                @endif
                <option value="">No Assignment</option>
                @foreach ($admins as $admin)
                    @if (!$application->assignedTo && !$application->assignedTo == $admin->id)
                        <option value="{{ $admin->id }}">{{ $admin->first_name }}</option>
                    @endif
                @endforeach
            </select>
        </form>
    @endif
</div>
