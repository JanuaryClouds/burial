<ul class="list-group list-group-flush fs-4">
    @if (!$records->sum(fn($c) => $c->claimant?->count()))
        <li class="list-group-item">No Requested Burial Assistances</li>
    @else
        @foreach ($records as $client)
            @php
                $burial = $client->claimant->burialAssistance ?? null;
                $deceased = $burial != null ? $burial->deceased : null;
                $claimant = $burial != null ? $burial->claimant : null;

                if ($burial != null) {
                    if ($burial->status == 'released') {
                        $statusClass = 'list-group-item-success';
                    } elseif ($burial->status == 'rejected') {
                        $statusClass = 'list-group-item-disabled';
                    } else {
                        $statusClass = '';
                    }
                }
            @endphp
            @if ($burial != null)
                <li class="list-group-item {{ $statusClass }}">
                    <a href="{{ route('burial.tracker', ['uuid' => $burial->id]) }}"
                        class="d-flex justify-content-between">
                        <span class="d-flex gap-3 fw-bold">
                            Burial for {{ $deceased->first_name }} {{ Str::charAt($deceased->middle_name, 0) }}.
                            {{ $deceased->last_name }} {{ $deceased?->suffix }}
                            <span class="badge rounded-pill text-bg-info">{{ ucfirst($burial->status) }}</span>
                        </span>
                        <span class="d-flex gap-2">
                            {{ $burial->created_at->format('F j, Y g:i A') }}
                        </span>
                    </a>
                </li>
            @endif
        @endforeach
    @endif
</ul>
