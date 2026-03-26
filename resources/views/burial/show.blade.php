@extends('layouts.metronic.admin')
@section('content')
    <div class="d-flex flex-column gap-4">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Progress of Burial Assistance</h4>
            </div>
            <div class="card-body d-flex flex-column gap-4">
                @include('burial.partials.progress')
                @include('burial.partials.timeline')
            </div>
        </div>
        @includeWhen($data->hasPendingClaimantChange(), 'burial.partials.claimant-change-request')
        @includeWhen(!$data->hasPendingClaimantChange(), 'burial.partials.menu')
        @include('burial.partials.process-update-modal')
        @include('burial.partials.reject-modal')
        <form action="{{ route('burial.update', $data->id) }}" method="post" id="contentForm" class="d-flex flex-column gap-4">
            @csrf
            @method('PUT')
            <div class="card">
                <div class="card-body">
                    @include('components.swa-form')
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    @include('components.claimant-form', [
                        'claimant' => $data->claimant,
                        'readonly' => $readonly,
                    ])
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    @include('components.deceased-form', [
                        'beneficiary' => $data->originalClaimant()?->client?->beneficiary,
                        'readonly' => $readonly,
                    ])
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    @include('components.burial-assistance-details-form', [
                        'burialAssistance' => $data,
                        'readonly' => $readonly,
                    ])
                </div>
            </div>
        </form>
        <div class="card">
            <div class="card-body">
                @include('client.partial.documents', [
                    'client' => $data->claimant?->client,
                    'readonly' => true,
                ])
            </div>
        </div>
    </div>
@endsection
