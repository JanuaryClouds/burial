@props(['lastLogs' => []])
<div class="card">
    <div class="card-header">
        <h4>Last updates you added</h4>
    </div>
    <div class="card-body">
        <div class="d-flex flex-column">
            @if ($lastLogs->isEmpty())
                <p class="text-muted">No Activity as of yet</p>
            @else
                @foreach ($lastLogs as $log)
                    <ul class="list-unstyled">
                        <div class="media mb-2 pb-2 border-bottom">
                            <div class="media-body">
                                <span class="float-right text-muted">{{ $log->created_at->diffForHumans() }}</span>
                                <a href="{{ route('admin.applications.manage', ['id' => $log->burialAssistance->id]) }}">
                                    <h5 class="mb-2">
                                        {{ $log->loggable?->description ?? '' }}
                                    </h5>
                                </a>
                                <span class="d-flex align-items-baseline justify-content-between mb-2">
                                    <span>
                                        <i class="fas fa-user mr-1"></i>
                                        {{ $log->burialAssistance->deceased->last_name }} {{ $log->burialAssistance->deceased->first_name }}
                                    </span>
                                    <span>
                                        <span class="badge badge-pill badge-info">
                                            {{ Str::ucfirst($log->burialAssistance->status) }}
                                        </span>
                                    </span>
                                </span>
                                @if (class_basename($log->loggable) === 'WorkflowStep')
                                    <div class="progress" style="height: 8px;">
                                        <div 
                                            class="progress-bar bg-primary" 
                                            style="width: {{ ($log->loggable->order_no / App\Models\WorkflowStep::all()->count()) * 100 }}%;" 
                                            role="progressbar" 
                                            aria-valuenow="25" 
                                            aria-valuemin="0" 
                                            aria-valuemax="100"
                                        ></div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </ul>
                @endforeach
            @endif
        </div>
    </div>
</div>