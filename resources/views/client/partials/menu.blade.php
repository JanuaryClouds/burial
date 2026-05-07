<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="mb-0">Client Actions</h1>
            <span class="d-flex align-items-center gap-3">
                @if (auth()->user()?->roles()->exists())
                    <a href="{{ route('clients.gis-form', ['id' => $client->id]) }}" class="btn btn-light" data-no-loader
                        target="_blank" rel="noopener noreferrer">
                        Generate GIS Form
                    </a>
                @endif
                @if ($released)
                    @if ($client->interviews?->count() == 0)
                        <button class="btn btn-primary" type="button" data-bs-toggle="modal"
                            data-bs-target="#set-schedule-modal">
                            Schedule an Interview
                        </button>
                    @endif
                    @if ($client?->assessment?->count() == 0)
                        <button class="btn btn-light" type="button" data-bs-toggle="modal"
                            data-bs-target="#assessment-modal">
                            Write an Assessment
                        </button>
                    @else
                        <button type="button" class="btn btn-secondary" data-bs-toggle="tooltip"
                            data-bs-placement="bottom" title="Client has been assessed">
                            Write an Assessment
                        </button>
                    @endif
                    @if ($client->assessment?->count() == 0)
                        <button type="button" class="btn btn-secondary" data-bs-toggle="tooltip"
                            data-bs-placement="bottom" title="Client must be assessed before creating a referral">
                            Referral
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-toggle="tooltip"
                            data-bs-placement="bottom" title="Client must be assessed before deciding a service">
                            Recommend a Service
                        </button>
                    @else
                        @if ($client->recommendation?->count() == 0 && $client->referral?->count() == 0)
                            <button type="button" class="btn btn-secondary" data-bs-toggle="modal"
                                data-bs-target="#referralModal">
                                Referral
                            </button>
                            <button class="btn btn-success" type="button" data-bs-toggle="modal"
                                data-bs-target="#services-modal">
                                Recommend a Service
                            </button>
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
                            <button type="button" class="btn btn-secondary" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="{{ $title }}">
                                Referral
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="{{ $title }}">
                                Recommend a Service
                            </button>
                        @endif
                    @endif
                @endif
            </span>
        </div>
    </div>
</div>
