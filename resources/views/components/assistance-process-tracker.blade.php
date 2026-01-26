@props(['burialAssistance', 'updateAverage'])
@php
    use App\Models\WorkflowStep;
    $latestStep = $burialAssistance->processLogs->sortBy('created_at')->last() ?? [];
    $currentStep = $latestStep ? $latestStep?->loggable?->order_no : 0;
    $totalWorkflowSteps = WorkflowStep::select('id')->get()->count();
    switch ($burialAssistance->status) {
        case 'processing':
            $badgeColor = 'primary text-white';
            break;
        case 'approved':
            $badgeColor = 'success text-white';
            break;
        case 'released':
            $badgeColor = 'success text-white';
            break;
        case 'rejected':
            $badgeColor = 'danger text-white';
            break;
        default:
            $badgeColor = 'secondary';
            break;
    }
@endphp
<div class="card">
    <div class="card-header">
        <div class="d-flex w-100 justify-content-between align-items-center">
            <h4 class="card-title">Burial Assistance Application Tracker</h4>
            <span class="badge rounded-pill fs-4 text-bg-{{ $badgeColor }}">
                {{ Str::ucfirst($burialAssistance->status) }}
            </span>
        </div>
    </div>
    <div class="card-body d-flex flex-column gap-3">
        <h3 class="fw-semibold">Progress</h3>
        <p class="fw-bold">Keep the provided mobile number as this is the main mode of communication. Updates to
            this application will be sent via the provided phone number.</p>
        <div class="progress">
            <div class="progress-bar" role="progressbar"
                style="width: {{ ($currentStep / $totalWorkflowSteps) * 100 }}%;" aria-valuenow="25" aria-valuemin="0"
                aria-valuemax="100"></div>
        </div>
        @include('burial.partials.timeline')
    </div>
</div>
