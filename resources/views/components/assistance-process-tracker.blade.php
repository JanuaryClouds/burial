@props(['burialAssistance', 'updateAverage'])
@php
    use App\Models\WorkflowStep;
    $latestStep = $processLogs->last();
    $currentStep = $latestStep?->loggable?->order_no;
    $totalWorkflowSteps = WorkflowStep::all()->count();
    switch ($burialAssistance->status) {
        case 'processing':
            $badgeColor = "primary";
            break;
        case 'approved':
            $badgeColor = "success";
            break;
        case 'released':
            $badgeColor = "success";
            break;
        case 'rejected':
            $badgeColor = "danger";
            break;
        default:
            $badgeColor = "secondary";
            break;
    }
@endphp
<div
    class="bg-white rounded p-4 shadow-sm"
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
                <span class="d-flex align-items-baseline">
                    <p class="fw-semibold mb-0 mr-2">Status: </p>
                    <span
                        class="badge rounded-pill badge-{{ $badgeColor }}"
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
        <div class="col mt-4">
            <ul class="list-group">
                <li
                    class="list-group-item d-flex justify-content-between align-items-center"
                    >
                    Submitted Application
                    <span class="badge badge-pill p-0">{{ $burialAssistance->created_at }}</span>
                </li>
                @foreach ($claimants as $claimant)
                    @foreach ($processLogs as $log)
                        <li
                            class="list-group-item d-flex justify-content-between align-items-center {{ $loop->last ? 'bg-primary text-white' : '' }}"
                        >
                            <p class="mb-0 {{ $loop->last ? 'fw-bold text-white' : 'text-black' }} d-flex align-items-baseline">
                                <b>{{ class_basename($log->loggable) === 'WorkflowStep' ? $log->loggable?->description : $log->comments }}</b>
                                @if (class_basename($log->loggable) === 'WorkflowStep' && $log->comments)
                                    <a class="ml-4 {{ $loop->last ? 'text-white' : '' }}" data-target="#show-comments-{{ $log->id }}" data-toggle="collapse" aria-expanded="false" aria-controls="show-comments-{{ $log->id }}">
                                        <i class="fa fa-comment-alt"></i>
                                    </a>
                                @endif
                                @if (class_basename($log->loggable) === 'WorkflowStep' && $log->extra_data)
                                    <a class="ml-4 {{ $loop->last ? 'text-white' : '' }}" data-target="#show-extra-data-{{ $log->id }}" data-toggle="collapse" aria-expanded="false" aria-controls="show-comments-{{ $log->id }}">
                                        <i class="fas fa-circle-info"></i>
                                    </a>
                                @endif
                            </p>
                            @if (class_basename($log->loggable) === 'WorkflowStep')
                                <span class="d-flex justify-content-center align-items-center">
                                    <span class="badge badge-pill p-0 m-0 d-flex align-items-center {{ $loop->last ? 'text-white fw-bold' : 'text-black' }}">
                                        In: {{ $log->date_in }}
                                        {{ $log->date_out ? '/ Out: ' . $log->date_out : '' }}
                                    </span>
                                    @if ((auth()->user() && auth()->user()->hasRole('admin')) && $loop->last && ($log->loggable->order_no !== $totalWorkflowSteps))
                                        <!-- An edit function would lose data integrity. To ensure data integrity, CSWDO must delete the log and create a new one -->
                                        <span class="d-flex align-items-center btn-toolbar">
                                            <x-delete-log :id="$burialAssistance->id" :stepId="$log->loggable->order_no" />
                                        </span>
                                    @endif
                                </span>
                            @else
                                <span class="badge badge-pill p-0 {{ $loop->last ? 'text-white fw-bold' : 'text-black' }}">{{ $log->date_in }}</span>
                            @endif
                        </li>
                        <div id="show-comments-{{ $log->id }}" class="collapse">
                            <li
                                class="list-group-item border-top-0 {{ $loop->last ? 'bg-primary text-white' : '' }}"
                            >
                                <p class="mb-0">{{ $log->comments }}</p>
                            </li>
                        </div>
                        @if (class_basename($log->loggable) === 'WorkflowStep' && $log->extra_data)
                            <div id="show-extra-data-{{ $log->id }}" class="collapse">
                                <li
                                    class="list-group-item border-top-0 {{ $loop->last ? 'bg-primary text-white' : '' }}"
                                >
                                    @foreach ($log->extra_data as $key => $subKey)
                                        @if (is_array($subKey))
                                            @foreach ($subKey as $data => $value)
                                                <p class="mb-0">
                                                    <b>{{ ucfirst(str_replace('_', ' ', $data)) }}</b> - {{ $value }}
                                                </p>
                                            @endforeach
                                        @elseif (is_string($subKey))
                                                <p class="mb-0">
                                                    <b>{{ ucfirst(str_replace('_', ' ', $key)) }}</b> - {{ $subKey }}
                                                </p>
                                        @endif
                                    @endforeach
                                </li>
                            </div>
                        @endif
                    @endforeach

                    @php
                        $lastLogDate = $claimant->processLogs->last()?->date_in;
                        $change = $claimantChanges->first(function($c) use ($lastLogDate) {
                            return $lastLogDate === null || $c->changed_at > $lastLogDate;
                        });
                    @endphp
                @endforeach
            </ul>
        </div>

        @if ($updateAverage && $updateAverage != null)
            <div class="col mt-4">
                <p class="text-muted">Average Processing Time: {{ $updateAverage }} hr per update</p>
            </div>
        @endif        
    </div>
</div>
