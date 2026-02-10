<div class="card shadow-sm multicolor-border">
    <div class="card-header">
        <h4 class="fw-bold card-title">Required Documents</h4>
    </div>
    <div class="card-body">
        <p class="text-muted">Please bring the following documents during the interview</p>
        <div class="row">
            <div class="col-12 col-lg-6 d-flex flex-column gap-2">
                <div class="card shadow-sm">
                    <div class="card-header" style="background-color: #1a4798">
                        <h5 class="card-title fw-bold text-white">For Burial Assistance</h5>
                    </div>
                    <div class="card-body">
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
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6 d-flex flex-column gap-4">
                <div class="card shadow-sm">
                    <div class="card-header" style="background-color: #f4c027">
                        <h5 class="card-title fw-bold text-white">For Libreng Libing</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @foreach ($funeralDocuments as $document)
                                <li class="list-group-item my-4 bg-body">
                                    <div class="d-flex justify-content-between">
                                        <span class="d-flex flex-column fs-4">
                                            <p class="fw-bold">{{ $document['name'] }}</p>
                                            <p class="text-muted"> {{ $document['description'] }}</p>
                                            <p class="text-muted fs-5">
                                                from {{ $document['source'] }}
                                            </p>
                                        </span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                <div class="card shadow-sm">
                    <div class="card-header" style="background-color: #ed1c24">
                        <h4 class="card-title fw-bold text-white">Notice</h4>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Please prepare the following before visiting our office. These are not
                            required
                            but
                            are convenient to have.</p>
                        <ul class="list-group list-group-numbered list-group-flush fs-5">
                            <li class="list-group-item">
                                Black Ballpoint Pen
                            </li>
                            <li class="list-group-item">
                                Active Mobile Phone Number
                            </li>
                            <li class="list-group-item">
                                Brown Envelope
                            </li>
                            <li class="list-group-item">
                                Your prescripted reading glasses
                            </li>
                            <li class="list-group-item">
                                Drinking Water
                            </li>
                            <li class="list-group-item">
                                Vacant time allotted for interview
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
