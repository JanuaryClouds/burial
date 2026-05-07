@extends('layouts.metronic.admin')
@section('content')
    <div class="d-flex flex-column gap-4">
        @includeWhen(
            $claimantChange == null || ($claimantChange != null && $claimantChange->status != 'pending'),
            'burial.partials.menu')
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
            @includeWhen(
                $claimantChange != null &&
                    $claimantChange->status == 'pending' &&
                    ($data->status != 'released' && $data->status != 'rejected' && $data->status != 'approved'),
                'burial.partials.claimant-change-request')
        @endcan
        @can('create-updates')
            @includeWhen(
                $data->status != 'released' &&
                    $data->status != 'rejected' &&
                    ($claimantChange == null ||
                        ($claimantChange != null && $claimantChange->status != 'pending')) &&
                    $next_step != null,
                'burial.partials.process-update-modal')
            @include('burial.partials.reject-modal')
        @endcan
        @if (
            $claimantChange == null &&
                ($data->status != 'released' && $data->status != 'rejected' && $data->status != 'approved') &&
                auth()->user()->can('create', [App\Models\ClaimantChange::class, $data]))
            @include('burial.partials.claimant-change-form')
        @endif
        @cannot('manage-content')
            <div class="card mt-10">
                <div class="card-body">
                    @include('client.partials.swa-form', [
                        'application' => $data,
                        'readonly' => true,
                    ])
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    @include('burial.partials.claimant-form', [
                        'claimant' => $currentClaimant,
                        'readonly' => true,
                    ])
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    @include('client.partials.beneficiary-info', [
                        'client' => $data->originalClaimant()?->client,
                        'readonly' => true,
                    ])
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    @include('burial.partials.details-form', [
                        'burialAssistance' => $data,
                        'readonly' => true,
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
        @endcannot
        @can('manage-content')
            <form action="{{ route('burial.update', $data->id) }}" method="post" id="contentForm"
                class="d-flex flex-column gap-4">
                @csrf
                @method('PUT')
                <div class="card mt-10">
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
                            'claimant' => $currentClaimant,
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
