<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="mb-0">Client Actions</h1>
            <div class="d-flex flex-wrap align-items-center gap-3">
                @role('staff')
                    <a href="{{ route('client.gis-form', ['id' => $client->id]) }}" class="btn btn-light" data-no-loader
                        target="_blank" rel="noopener noreferrer">
                        Generate GIS Form
                    </a>
                    @if ($released)
                        @if ($client?->interviews?->count() === 0)
                            @can('create', [App\Models\Interview::class, $client])
                                <button class="btn btn-primary" type="button" data-bs-toggle="modal"
                                    data-bs-target="#set-schedule-modal">
                                    Schedule an Interview
                                </button>
                            @else
                                <button class="btn btn-secondary" type="button" data-bs-toggle="tooltip"
                                    data-bs-placement="bottom" title="You do not have permissions to schedule an interview">
                                    Schedule an Interview
                                </button>
                            @endcan
                        @else
                            <button type="button" class="btn btn-secondary" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="Client has been scheduled for an interview">
                                Schedule an Interview
                            </button>
                        @endif
                        @if ($client?->assessment?->count() == 0)
                            @can('create', [App\Models\ClientAssessment::class, $client])
                                <button class="btn btn-primary" type="button" data-bs-toggle="modal"
                                    data-bs-target="#assessment-modal">
                                    Write an Assessment
                                </button>
                            @else
                                <button type="button" class="btn btn-secondary" data-bs-toggle="tooltip"
                                    data-bs-placement="bottom" title="You do not have permissions to assess a client">
                                    Write an Assessment
                                </button>
                            @endcan
                        @else
                            <button type="button" class="btn btn-secondary" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="Client has been assessed">
                                Write an Assessment
                            </button>
                        @endif
                        @if ($client?->assessment?->count() == 0)
                            @can('create', [App\Models\Referral::class, $client])
                                <button type="button" class="btn btn-secondary" data-bs-toggle="tooltip"
                                    data-bs-placement="bottom" title="Client must be assessed before creating a referral">
                                    Referral
                                </button>
                            @else
                                <button type="button" class="btn btn-secondary" data-bs-toggle="tooltip"
                                    data-bs-placement="bottom" title="You do not have permissions to create a referral">
                                    Referral
                                </button>
                            @endcan
                            @can('create', [App\Models\ClientRecommendation::class, $client])
                                <button type="button" class="btn btn-secondary" data-bs-toggle="tooltip"
                                    data-bs-placement="bottom" title="Client must be assessed before deciding a service">
                                    Recommend a Service
                                </button>
                            @else
                                <button type="button" class="btn btn-secondary" data-bs-toggle="tooltip"
                                    data-bs-placement="bottom" title="You do not have permissions to recommend a service">
                                    Recommend a Service
                                </button>
                            @endcan
                        @else
                            @if ($client?->recommendation?->count() == 0 && $client?->referral?->count() == 0)
                                @can('create', [App\Models\Referral::class, $client])
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#referralModal">
                                        Referral
                                    </button>
                                @else
                                    <button type="button" class="btn btn-secondary" data-bs-toggle="tooltip"
                                        data-bs-placement="bottom" title="You do not have permissions to create a referral">
                                        Referral
                                    </button>
                                @endcan
                                @can('create', [App\Models\ClientRecommendation::class, $client])
                                    <button class="btn btn-success" type="button" data-bs-toggle="modal"
                                        data-bs-target="#services-modal">
                                        Recommend a Service
                                    </button>
                                @else
                                    <button type="button" class="btn btn-secondary" data-bs-toggle="tooltip"
                                        data-bs-placement="bottom" title="You do not have permissions to recommend a service">
                                        Recommend a Service
                                    </button>
                                @endcan
                            @elseif ($client?->recommendation || $client?->referral)
                                @php
                                    $title = 'A service cannot be recommended to this client';
                                    if ($client?->recommendation) {
                                        $title = 'Client has been recommended a service';
                                    }
                                    if ($client?->referral) {
                                        $title = 'Client has been referred to another department';
                                    }
                                @endphp
                                @if ($client?->referral)
                                    <button type="button" class="btn btn-secondary" data-bs-toggle="tooltip"
                                        data-bs-placement="bottom" title="{{ $title }}">
                                        Referral
                                    </button>
                                @endif
                                @if ($client?->recommendation)
                                    <button type="button" class="btn btn-secondary" data-bs-toggle="tooltip"
                                        data-bs-placement="bottom" title="{{ $title }}">
                                        Recommend a Service
                                    </button>
                                @endif
                            @endif
                        @endif
                    @endif
                @endrole
            </div>
        </div>
    </div>
</div>
