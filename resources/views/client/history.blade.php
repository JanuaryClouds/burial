@extends('layouts.metronic.guest')
@section('content')
    <div class="container-xxl min-vh-100 my-10">
        <div class="d-flex flex-column gap-4">
            <div class="card">
                <div class="card-header collapsible cursor-pointer rotate d-flex align-items-center justify-content-between"
                    data-bs-toggle="collapse" data-bs-target="#client_info_card_body">
                    <h4 class="card-title">
                        {{ $client->first_name }}
                        {{ Str::limit($client->middle_name, 1, '.') ?? null }}
                        {{ $client->last_name }}
                        {{ $client?->suffix }}
                    </h4>
                    <span class="d-flex gap-2">
                        <p class="text-muted mb-0">(Latest record - {{ $client->created_at->format('m/d/Y') }})</p>
                        <i class="ki-duotone ki-down fs-1"></i>
                    </span>
                </div>
                <div id="client_info_card_body" class="collapse">
                    <div class="card-body">
                        @include('client.partial.client-info')
                    </div>
                </div>
                <div class="card-footer pb-0 pt-4">
                    <ul class="nav nav-tabs nav-line-tabs nav-stretch fs-4 border-bottom-0">
                        <li class="nav-item">
                            <a href="#applications_tab" class="nav-link text-gray-700 pb-6" data-bs-toggle="tab">
                                Applications
                                <span
                                    class="badge rounded-pill text-bg-light ms-2">{{ $records ? $records->count() : 0 }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#interviews_tab" class="nav-link active text-gray-700 pb-6" data-bs-toggle="tab">
                                Interviews
                                <span
                                    class="badge rounded-pill text-bg-light ms-2">{{ $records->sum(fn($c) => $c->interviews?->count()) ?? 0 }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#burials_tab" class="nav-link text-gray-700 pb-6" data-bs-toggle="tab">
                                Burial Assistances
                                <span
                                    class="badge rounded-pill text-bg-light ms-2">{{ $records->sum(fn($c) => $c->claimant?->burialAssistance?->count()) ?? 0 }}</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="#funerals_tab" class="nav-link text-gray-700 pb-6" data-bs-toggle="tab">
                                Funeral Assistances
                                <span
                                    class="badge rounded-pill text-bg-light ms-2">{{ $records->sum(fn($c) => $c->funeralAssistance?->count()) ?? 0 }}</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="tab-content" id="tabContent">
                        <div id="applications_tab" class="tab-pane fade" role="tabpanel">
                            @include('client.partial.application-history')
                        </div>
                        <div id="interviews_tab" class="tab-pane fade show active" role="tabpanel">
                            @include('client.partial.interview-history')
                        </div>
                        <div id="burials_tab" class="tab-pane fade" role="tabpanel">
                            @include('client.partial.burial-history')
                        </div>
                        <div id="funerals_tab" class="tab-pane fade" role="tabpanel">
                            @include('funeral.partials.history')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
