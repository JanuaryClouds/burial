<div class="d-flex flex-column gap-2">
    <h4 class="fs-1">Required Documents</h4>

    <div class="d-flex flex-column gap-2">
        <h5>For Burial Assistance</h5>
        <ul class="list-group list-group-flush">
            @foreach ($burialDocuments as $document)
                <li class="list-group-item my-4 bg-body">
                    <div class="d-flex justify-content-between">
                        <span class="d-flex flex-column fs-4">
                            <p class="fw-bold">{{ $document['name'] }}</p>
                            <p class="text-muted"> {{ $document['description'] }}</p>
                            <p class="text-muted fs-5">
                                from {{ $document['source'] }}
                            </p>
                        </span>
                        <span class="d-flex gap-1">
                            @if ($document['is_muslim'])
                                <div>
                                    <span class="badge bg-danger text-white">For Muslim</span>
                                </div>
                            @endif
                        </span>
                    </div>
                </li>
            @endforeach
        </ul>
        <h5>For Funeral Assistance</h5>
        <ul class="list-group list-group-flush">
            @foreach ($funeralDocuments as $document)
                <li class="list-group-item bg-body">
                    <div class="d-flex justify-content-between">
                        <span class="d-flex flex-column fs-4">
                            <p class="fw-bold">{{ $document['name'] }}</p>
                            <p class="text-muted"> {{ $document['description'] }}</p>
                            <p class="text-muted fs-5">
                                from {{ $document['source'] }}
                            </p>
                        </span>
                        @if ($document['is_mandatory'])
                            <div>
                                <span class="badge bg-danger text-white">Required</span>
                            </div>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
    </div>
</div>
