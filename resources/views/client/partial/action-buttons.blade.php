<div class="d-flex justify-content-end gap-3">
    <a href="{{ route('clients.gis-form', ['id' => $client->id]) }}" class="btn btn-light" data-no-loader>
        Generate GIS Form
    </a>
    @if ($released)
        @if ($client->interviews->count() == 0)
            <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal"
                data-bs-target="#set-schedule-modal">
                Schedule an Interview
            </button>
        @endif
        @if ($client?->assessment->count() == 0)
            <button class="btn btn-light" type="button" data-bs-toggle="modal" data-bs-target="#assessment-modal">
                Assessment
            </button>
        @else
            <button type="button" class="btn btn-secondary" data-bs-toggle="tooltip" data-bs-placement="bottom"
                title="Client has been assessed">
                Assessment
            </button>
        @endif
        @if ($client->assessment->count() == 0 && $client->recommendation->count() == 0)
            <button type="button" class="btn btn-secondary" data-bs-toggle="tooltip" data-bs-placement="bottom"
                title="Client must be assessed before deciding a service">
                Services
            </button>
        @else
            @if ($client->recommendation->count() == 0)
                <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#services-modal">
                    Services
                </button>
            @elseif ($client?->recommendation)
                <button type="button" class="btn btn-secondary" data-bs-toggle="tooltip" data-bs-placement="bottom"
                    title="Client has been assessed and given a service">
                    Services
                </button>
            @endif
        @endif
    @endif
</div>
