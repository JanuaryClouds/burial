@extends('layouts.app')
@section('content')
    <div class="d-flex flex-column gap-4">
        @include('burial.partials.menu')
        @if ($data->status != 'released' && $next_step != null)
            @include('burial.partials.process-update-modal')
        @endif
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
        @if (
            $claimantChange == null &&
                ($data->status != 'released' && $data->status != 'approved') &&
                $current_step < 9 &&
                auth()->user()->can('create', [App\Models\ClaimantChange::class, $data]))
            @include('burial.partials.claimant-change-form')
        @endif
        @unlessrole('superadmin')
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
            @if ($claimantChange != null)
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Original Claimant</h4>
                    </div>
                    <div class="card-body">
                        @include('burial.partials.claimant-form', [
                            'claimant' => $originalClaimant,
                            'readonly' => true,
                        ])
                    </div>
                </div>
            @endif
            <div class="card">
                <div class="card-body">
                    @include('client.partials.beneficiary-info', [
                        'client' => $originalClaimant?->client,
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
                        'client' => $originalClaimant?->client,
                        'readonly' => true,
                    ])
                </div>
            </div>
        @endunlessrole
        @role('superadmin')
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
                @if ($claimantChange != null)
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">Original Claimant</h4>
                        </div>
                        <div class="card-body">
                            @include('burial.partials.claimant-form', [
                                'claimant' => $originalClaimant,
                                'readonly' => true,
                            ])
                        </div>
                    </div>
                @endif
                <div class="card">
                    <div class="card-body">
                        @include('client.partials.beneficiary-info', [
                            'client' => $originalClaimant?->client,
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
                            'client' => $originalClaimant->client,
                            'readonly' => true,
                        ])
                    </div>
                </div>
            </form>
        @endrole
    </div>
@endsection
