@extends('layouts.metronic.admin')
<title>Manage {{ $application->tracking_no }}</title>
@section('content')
    <div class="d-flex flex-column gap-5">
        <x-assistance-process-tracker :burialAssistance="$application" :updateAverage="$updateAverage" />
        <x-application-manager :application="$application" />
        <form action="{{ route('burial.update', $application->id) }}" method="post" id="contentForm"
            class="d-flex flex-column gap-4">
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
                        'claimant' => $application->claimant,
                        'readonly' => $readonly,
                    ])
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    @include('components.deceased-form', [
                        'deceased' => $application->deceased,
                        'readonly' => $readonly,
                    ])
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    @include('components.burial-assistance-details-form', [
                        'burialAssistance' => $application,
                        'readonly' => $readonly,
                    ])
                </div>
            </div>
        </form>
        <div class="card">
            <div class="card-body">
                @include('client.partial.documents', [
                    'client' => $application->claimant->client,
                    'readonly' => true,
                ])
            </div>
        </div>
        @include('components.applications-modal-loader', [
            'application_id' => $application->id,
        ])
    </div>
@endsection
