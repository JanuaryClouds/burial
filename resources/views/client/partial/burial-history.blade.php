<ul class="list-group list-group-flush">
    @if (!$records->first()->claimant)
        <li class="list-group-item">No Requested Burial Assistances</li>
    @else
        @foreach ($records as $client)
            @php
                $burial = $client->claimant->burialAssistance;

                if ($burial->status == 'released') {
                    $statusClass = 'list-group-item-success';
                } elseif ($burial->status == 'rejected') {
                    $statusClass = 'list-group-item-disabled';
                } else {
                    $statusClass = '';
                }
            @endphp
            <li class="list-group-item {{ $statusClass }}">
                <strong>{{ $burial->created_at->format('F j, Y g:i A') }}</strong> -
                Status: {{ ucfirst($burial->status) }}
            </li>
        @endforeach
    @endif
</ul>
