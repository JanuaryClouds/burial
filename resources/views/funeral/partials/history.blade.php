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
                    <span class="d-flex fs-6">
                        <p class="mb-0">Submitted in {{ $funeral->created_at->format('F j, Y g:i A') }}</p>
                        <p class="text-gray-700 mb-0">
                            {{ ', approved ' . \Carbon\Carbon::parse($funeral?->approved_at)->diffForHumans($funeral->created_at) ?? '' }}
                        </p>
                        <p class="text-success mb-0">
                            {{ ', forwarded to Taguig Public Cemetery ' . \Carbon\Carbon::parse($funeral?->forwarded_at)->format('F j, Y g:i A') ?? '' }}
                        </p>
                    </span>
                </div>
            </li>
        @endforeach
    @endif
</ul>
