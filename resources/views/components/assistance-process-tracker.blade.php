@props(['burialAssistance'])
@php
    use App\Models\WorkflowStep;

    $latestStep = $processLogs->last();
    $currentStep = $latestStep?->workflowStep->order_no + 1;
    $totalWorkflowSteps = WorkflowStep::all()->count() + 1;
    switch ($burialAssistance->status) {
        case 'processing':
            $badgeColor = "text-bg-primary";
            break;
        case 'approved':
            $badgeColor = "text-bg-success";
            break;
        case 'released':
            $badgeColor = "text-bg-success";
            break;
        case 'rejected':
            $badgeColor = "text-bg-danger";
            break;
        default:
            $badgeColor = "text-bg-secondary";
            break;
    }
@endphp
<div
    class="container bg-white rounded p-4 shadow"
>
    <div
        class="row flex-column justify-content-start align-items-end g-2"
    >
        <div class="col">
            <h1>Burial Assistance Application Tracker</h1>
        </div>
        <div class="col">
            <p class="fw-bold">Keep the provided mobile number as this is the main mode of communication. Updates to this application will be sent via the provided phone number.</p>
            <div class="d-flex flex-wrap gap-4 justify-content-between align-items-baseline sticky-top">
                <h3 class="fw-semibold">Progress</h3>
                <span class="d-flex gap-2 align-items-baseline">
                    <p class="fw-semibold mb-0">Status: </p>
                    <span
                        class="badge rounded-pill {{ $badgeColor }}"
                        >{{ Str::ucfirst($burialAssistance->status) }}</span
                    >
                </span>
            </div>
        </div>
        <div class="col">
            <div class="progress">
                <div
                    class="progress-bar"
                    role="progressbar"
                    style="width: {{ ($currentStep / $totalWorkflowSteps) * 100 }}%;"
                    aria-valuenow="25"
                    aria-valuemin="0"
                    aria-valuemax="100"
                ></div>
            </div>
        </div>
        <div class="col">
            <ul class="list-group">
                <li
                    class="list-group-item d-flex justify-content-between align-items-center"
                >
                    Submitted Application
                    <span class="badge bg-secondary badge-pill">{{ $burialAssistance->created_at }}</span>
                </li>
                @foreach ($processLogs as $log)
                    <li
                        class="list-group-item d-flex justify-content-between align-items-center"
                    >
                        {{ $log->workflowStep->name }}
                        <span class="badge bg-secondary badge-pill">{{ $log->date_in }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
        
    </div>
</div>
