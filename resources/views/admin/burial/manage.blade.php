@extends('layouts.metronic.admin')
<title>Manage {{ $application->tracking_no }}</title>
@section('content')
    <div class="d-flex flex-column gap-5">
        <x-assistance-process-tracker :burialAssistance="$application" :updateAverage="$updateAverage" />
        <x-application-manager :application="$application" />
        <x-swa-form :application="$application" :disabled="false" :readonly="false" />
        <x-deceased-form :deceased="$application->deceased" disabled="true" readonly="true" />

        @if ($application->claimantChanges->count() > 0 && $application->claimantChanges->last()->status == 'approved')
            <x-claimant-form :claimant="$application->claimantChanges->last()->newClaimant" disabled="true" readonly="true" />
            <button class="btn btn-secondary mt-4" data-target="#old-claimant" data-toggle="collapse" aria-expanded="false"
                aria-controls="old-claimant">
                <i class="fas fa-diagram-predecessor"></i>
                Previous Claimant
            </button>
            <div id="old-claimant" class="collapse mt-2 p-4">
                <x-claimant-form :claimant="$application->claimant" disabled="true" readonly="true" />
            </div>
        @else
            <x-claimant-form :claimant="$application->claimant" disabled="true" readonly="true" />
        @endif
        <x-burial-assistance-details-form :burialAssistance="$application" disabled="true" readonly="true" />
        <x-burial-assistance-image-requirements :burialAssistance="$application" disabled="true" readonly="true" :files="$files" />
        <x-applications-modal-loader :application_id="$application->id" />
    </div>
@endsection
