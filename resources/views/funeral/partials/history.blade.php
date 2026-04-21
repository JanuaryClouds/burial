<ul class="list-group list-group-flush fs-4">
    @if ($records->sum(fn($c) => $c->funeralAssistance?->count()) == 0)
        <li class="list-group-item bg-body-secondary rounded-pill">No Requested Libreng Libing</li>
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
                @if ($funeral->tracker)
                    <form action="{{ route('tracker.match') }}" method="post">
                        @csrf
                        <input type="hidden" name="tracking_no" value="{{ $client->tracking_no }}">
                        <input type="hidden" name="code" value="{{ $funeral->tracker?->code }}">
                        <button type="submit" class="btn w-100 d-flex justify-content-between">
                            <span class="d-flex gap-3 fw-bold">
                                Funeral for {{ $beneficiary->fullname() }}
                            </span>
                            <span class="d-flex fs-6">
                                <p class="mb-0">Submitted in {{ $funeral->created_at->format('F j, Y g:i A') }}</p>
                            </span>
                        </button>
                    </form>
                @endif
                <div class="d-flex justify-content-between align-items-center">
                </div>
            </li>
        @endforeach
    @endif
</ul>
