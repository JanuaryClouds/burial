<ul class="list-group list-group-flush">
    @if ($interviews->isEmpty())
        <li class="list-group-item">No scheduled interviews.</li>
    @endif
    @foreach ($interviews as $interview)
        @php
            if ($interview->status == 'done') {
                $statusClass = 'list-group-item-success';
            } elseif ($interview->schedule->diffInHours(Carbon\Carbon::now()) <= 24) {
                $statusClass = 'list-group-item-warning';
            } else {
                $statusClass = 'disabled';
            }
        @endphp
        <li class="list-group-item {{ $statusClass }}">
            <strong>{{ $interview->schedule->format('F j, Y g:i A') }}</strong> -
            Status: {{ ucfirst($interview->status) }}
        </li>
    @endforeach
</ul>
