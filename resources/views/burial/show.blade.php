@extends('layouts.metronic.admin')
@section('content')
    <div class="d-flex flex-column gap-4">
        @includeWhen(!$data->hasPendingClaimantChange(), 'burial.partials.menu')
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Progress of Burial Assistance</h4>
            </div>
            <div class="card-body d-flex flex-column gap-4">
                @include('burial.partials.progress')
                @include('burial.partials.timeline')
                @include('burial.partials.claimant-change-status-alert')
            </div>
        </div>
        @can('edit-claimant-change-requests')
            @includeWhen($data->hasPendingClaimantChange(), 'burial.partials.claimant-change-request')
        @endcan
        @can('create-updates')
            @includeWhen(
                $data->status != 'released' &&
                    $data->status != 'rejected' &&
                    !$data->hasPendingClaimantChange() &&
                    $next_step != null,
                'burial.partials.process-update-modal')
            @include('burial.partials.reject-modal')
        @endcan
        @can('manage-content')
            <form action="{{ route('burial.update', $data->id) }}" method="post" id="contentForm" class="d-flex flex-column gap-4">
                @csrf
                @method('PUT')
                <div class="card">
                    <div class="card-body">
                        @include('client.partials.swa-form', [
                            'application' => $data,
                            'readonly' => $readonly,
                        ])
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('burial.partials.claimant-form', [
                            'claimant' => $data->claimant,
                            'readonly' => $readonly,
                        ])
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('client.partials.beneficiary-info', [
                            'client' => $data->originalClaimant()?->client,
                            'readonly' => $readonly,
                        ])
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('burial.partials.details-form', [
                            'burialAssistance' => $data,
                            'readonly' => $readonly,
                        ])
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        @include('client.partials.documents', [
                            'client' => $data->originalClaimant()?->client,
                            'readonly' => true,
                        ])
                    </div>
                </div>
            </form>
        @endcan
    </div>
@endsection
