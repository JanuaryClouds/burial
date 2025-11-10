@if ($client->interviews->count() > 0)
    @if ($client->interviews->first()->status != 'done')
        @if (Carbon\Carbon::parse($client->interviews->first()->schedule)->day == Carbon\Carbon::now()->day)
            @if (Carbon\Carbon::parse($client->interviews->first()->schedule)->diffInHours(Carbon\Carbon::now()) >= 1)
                <div class="alert alert-dark" role="alert">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>
                            It has been more than an hour since this client's interview. It was set on
                            {{ Carbon\Carbon::parse($client->interviews->first()->schedule)->format('g:i A') }}
                        </span>
                        <form action="{{ route('clients.interview.schedule.done', ['id' => $client->id]) }}" method="post" class="mb-0">
                            @csrf
                            <button class="btn btn-info" type="button" data-toggle="modal" data-target="#set-schedule-modal">Schedule a new interview</button>
                            <button class="btn btn-success" type="submit">Mark Interview as Done</button>
                        </form>
                    </div>
                </div>
            @elseif (Carbon\Carbon::parse($client->interviews->first()->schedule)->diffInHours(Carbon\Carbon::now()) <= 1)
                <div class="alert alert-warning" role="alert">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fas fa-bell"></i>
                            This client is scheduled for an interview today at
                            {{ Carbon\Carbon::parse($client->interviews->first()->schedule)->format('g:i A') }}
                        </span>
                        <form action="{{ route('clients.interview.schedule.done', ['id' => $client->id]) }}" method="post" class="mb-0">
                            @csrf
                            <button class="btn btn-light" type="submit">Mark Interview as Done</button>
                        </form>
                    </div>
                </div>
            @else
                <div class="alert alert-primary" role="alert">
                    <i class="fas fa-bell"></i>
                    This client is scheduled for an interview today at
                    {{ Carbon\Carbon::parse($client->interviews->first()->schedule)->format('g:i A') }}
                </div>
            @endif
        @else
            <div class="alert alert-info" role="alert">
                <i class="fas fa-bell"></i>
                This client is scheduled for an interview.
                It is set to begin at 
                {{ Carbon\Carbon::parse($client->interviews->first()->schedule)->format('M d, Y g:i A') }}
            </div>
        @endif
    @endif
@endif

