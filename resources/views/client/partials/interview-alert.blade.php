@if ($client->interviews->count() > 0)
    @php
        $interview = $client->interviews->first();
    @endphp
    @if ($interview->schedule)
        @php
            $schedule = Carbon\Carbon::parse($interview->schedule);
            $now = Carbon\Carbon::now();
        @endphp
    @endif
    @if ($schedule->isToday())
        @php
            $diffInHours = $schedule->diffInHours($now, false);
        @endphp
        @if ($diffInHours > 1)
            <div class="alert alert-dark" role="alert">
                The interview was scheduled more than an hour ago. ({{ $schedule->format('g:i A') }})
            </div>
        @elseif (abs($diffInHours) <= 1)
            <div class="alert alert-warning" role="alert">
                The client is scheduled to have an interview today. ({{ $schedule->format('g:i A') }})
            </div>
        @else
            <div class="alert alert-primary" role="alert">
                The client is scheduled to have an interview today at {{ $schedule->format('g:i A') }}.
            </div>
        @endif
    @else
        @if ($schedule->isPast())
            <div class="alert alert-secondary" role="alert">
                The client had an interview scheduled on {{ $schedule->format('M d, Y g:i A') }}.
            </div>
        @else
            <div class="alert alert-info" role="alert">
                The client is scheduled to have an interview on {{ $schedule->format('M d, Y g:i A') }}.
            </div>
        @endif
    @endif
@endif
