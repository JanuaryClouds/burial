@props(['lastLogs' => []])
<div class="card">
    <div class="card-header">
        @if (auth()->user()->isAdmin())
            <h4>Last updates you added</h4>
        @else
            <h4>Recent Activity</h4>
        @endif
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
                                @if (auth()->user()->isAdmin())
                                    <a href="{{ route('admin.applications.manage', ['id' => $log->burialAssistance->id]) }}">
                                        <h5 class="mb-2">
                                            {{ $log->loggable?->description ?? '' }}
                                        </h5>
                                    </a>
                                @else
                                    <h5 class="mb-2">
                                        {{ $log->burialAssistance->deceased->last_name }}, {{ $log->burialAssistance->deceased->first_name }}
                                    </h5>
                                @endif
                                <span class="d-flex align-items-baseline justify-content-between mb-2">
                                    @if (auth()->user()->isAdmin())
                                        <span>
                                            <i class="fas fa-user mr-1"></i>
                                            {{ $log->burialAssistance->deceased->last_name }} {{ $log->burialAssistance->deceased->first_name }}
                                        </span>
                                    @else
                                        <span>
                                            <i class="fas fa-user-nurse mr-1"></i>
                                            {{ $log->addedBy?->first_name }} {{ $log->addedBy?->last_name }}
                                        </span>
                                    @endif
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