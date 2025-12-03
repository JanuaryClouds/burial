<div class="d-flex flex-column gap-2">
    @if ($records->isEmpty())
        <div class="alert alert-secondary" role="alert">
            No application history available for this client.
        </div>
    @else
        @foreach ($records as $client)
            <div class="card bg-body-secondary">
                <div class="card-header collapsible cursor-pointer rotate d-flex align-items-center justify-content-between"
                    data-bs-toggle="collapse" data-bs-target="#client_info_card_body-{{ $loop->index }}">
                    <h4 class="card-title">
                        Submitted on {{ $client->created_at->format('m/d/Y') }}
                    </h4>
                    <i class="ki-duotone ki-down fs-1"></i>
                </div>
                <div class="card-body collapse" id="client_info_card_body-{{ $loop->index }}">
                    @include('client.partial.client-info')
                </div>
            </div>
        @endforeach
    @endif
</div>
