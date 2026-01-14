<div class="d-flex justify-content-end gap-3">
    <a href="{{ route('clients.gis-form', ['id' => $client->id]) }}" class="btn btn-info" data-no-loader>
        Generate GIS Form
    </a>
    @if (!$readonly)
        @if ($client->interviews->count() == 0)
            <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="modal"
                data-bs-target="#set-schedule-modal">
                Schedule an Interview
            </button>
        @endif
        <button class="btn btn-secondary" type="button" data-bs-toggle="modal" data-bs-target="#assessment-modal">
            Assessment
        </button>
        @if ($client->assessment->count() > 0)
            <button class="btn btn-success" type="button" data-bs-toggle="modal" data-bs-target="#services-modal">
                Services
            </button>
        @endif
    @endif
</div>
