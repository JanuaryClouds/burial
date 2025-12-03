<div class="d-flex flex-column gap-2">
    @if ($records->sum(fn($c) => $c->interviews->count()) == 0)
        <div class="alert alert-secondary" role="alert">
            No interview history available for this client.
        </div>
    @else
        @foreach ($records as $client)
            @foreach ($client->interviews as $interview)
                @php
                    if ($interview->status == 'done') {
                        $statusClass = 'alert-success';
                    } elseif (
                        \Carbon\Carbon::now()->lessThanOrEqualTo($interview->schedule) &&
                        $interview->status != 'done'
                    ) {
                        $statusClass = 'alert-danger';
                    } else {
                        $statusClass = 'alert-info';
                    }
                @endphp
                <div class="alert {{ $statusClass }}" role="alert">
                    <strong>{{ \Carbon\Carbon::parse($interview->schedule)->format('F j, Y g:i A') }}</strong>
                    Status: {{ ucfirst($interview->status) }}
                </div>
            @endforeach
        @endforeach
    @endif
</div>
