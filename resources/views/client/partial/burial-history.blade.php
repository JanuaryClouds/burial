<ul class="list-group list-group-flush fs-4 gap-4">
    @if (!$records->sum(fn($c) => $c->claimant?->count()))
        <li class="list-group-item">No Requested Burial Assistances</li>
    @else
        @php
            $clients = $records->filter(fn($client) => $client->claimant)->values();
        @endphp
        @foreach ($clients as $client)
            @php
                $claimant = $client->claimant;
                $burial = $claimant->burialAssistance;
                $beneficiary = $client != null ? $client->beneficiary : null;

                if ($claimant != null) {
                    if ($burial->status == 'released') {
                        $statusClass = 'list-group-item-success';
                    } elseif ($burial->status == 'rejected') {
                        $statusClass = 'list-group-item-disabled';
                    } else {
                        $statusClass = 'bg-secondary';
                    }
                }
            @endphp
            @if ($burial != null)
                <li class="list-group-item rounded-pill {{ $statusClass }}">
                    <form action="{{ route('tracker.match') }}" method="post">
                        @csrf
                        <input type="hidden" name="tracking_no" value="{{ $client->tracking_no }}">
                        <input type="hidden" name="code" value="{{ $burial->tracker->code }}">
                        <button type="submit" class="btn w-100 d-flex justify-content-between">
                            <span class="d-flex gap-3 fw-bold">
                                Burial for {{ $beneficiary->fullname() }}
                                <span class="badge rounded-pill text-bg-info">{{ ucfirst($burial->status) }}</span>
                            </span>
                            <span class="d-flex gap-2">
                                {{ $burial->created_at->format('F j, Y g:i A') }}
                            </span>
                        </button>
                    </form>
                </li>
            @endif
        @endforeach
    @endif
</ul>
