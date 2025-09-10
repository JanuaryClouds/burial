@props([
    'application' => []
])

@if ($processLogs->count() == 0 || ($application->status != 'rejected' || $application->status != 'released'|| $application->status != 'approved'))
    <div class="bg-white rounded shadow-sm p-4">
        <div class="d-flex justify-content-end">
            <button class="btn btn-primary mr-2" type="button" data-toggle="modal" data-target="#add-process">Add Progress Update</button>
            <button class="btn btn-danger" type="button" data-toggle="modal" data-target="#confirm-rejection">Reject Application</button>
        </div>
    </div>
@endif
