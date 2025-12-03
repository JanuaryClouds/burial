<ul class="list-group list-group-flush fs-4">
    @if ($records->sum(fn($c) => $c->funeralAssistance?->count()) == 0)
        <li class="list-group-item">No Requested Funeral Assistances</li>
    @else
        @foreach ($records as $client)
            @php
                $funeral = $client->funeralAssistance;
                $beneficiary = $client->beneficiary;

                if ($funeral == null) {
                    continue;
                }
                if ($funeral->forwarded_at != null) {
                    $statusClass = 'list-group-item-success';
                } else {
                    $statusClass = 'list-group-item-light';
                }
            @endphp
            <li class="list-group-item rounded-pill {{ $statusClass }}">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="d-flex gap-3 fw-bold">
                        Funeral for {{ $beneficiary->first_name }} {{ Str::charAt($beneficiary->middle_name, 0) }}.
                        {{ $beneficiary->last_name }} {{ $beneficiary?->suffix }}
                    </span>
                    <span class="d-flex gap-2">
                        <p class="text-gray-700">{{ $funeral->approved_at?->format('F j, Y g:i A') ?? '' }}</p>
                        <p class="text-success">{{ $funeral->forwarded_at?->format('F j, Y g:i A') ?? '' }}</p>
                        {{ $funeral->created_at->format('F j, Y g:i A') }}
                    </span>
                </div>
            </li>
        @endforeach
    @endif
</ul>
