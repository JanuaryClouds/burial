@extends('layouts.stisla.admin')
<title>Manage {{ $application->tracking_no }}</title>
@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Manage {{ $application->tracking_no }}</h1>
        </div>
    </section>
    <section class="section">
        <div class="section-body">
            <div class="row flex flex-column gap-5">
                <div class="col">
                    <x-assistance-process-tracker :burialAssistance="$application" :updateAverage="$updateAverage"/>
                </div>
                <div class="col">
                    <x-application-manager :application="$application"/>
                </div>
                <div class="col mt-5">
                    <x-swa-form :application="$application" :disabled="false" :readonly="false"/>
                </div>
                <div class="col mt-4">
                    <x-deceased-form :deceased="$application->deceased" disabled="true" readonly="true"/>
                </div>
                <div class="col">
                    @if ($application->claimantChanges->count() > 0 && $application->claimantChanges->last()->status == 'approved')
                        <x-claimant-form :claimant="$application->claimantChanges->last()->newClaimant" disabled="true" readonly="true"/>
                        <button class="btn btn-secondary mt-4" data-target="#old-claimant" data-toggle="collapse" aria-expanded="false" aria-controls="old-claimant">
                            <i class="fas fa-diagram-predecessor"></i>
                            Previous Claimant
                        </button>
                        <div id="old-claimant" class="collapse mt-2 p-4">
                            <x-claimant-form :claimant="$application->claimant" disabled="true" readonly="true"/>
                        </div>
                    @else
                        <x-claimant-form :claimant="$application->claimant" disabled="true" readonly="true"/>
                    @endif
                </div>
                <div class="col mt-4">
                    <x-burial-assistance-details-form :burialAssistance="$application" disabled="true" readonly="true"/>
                </div>
                <div class="col">
                    <x-burial-assistance-image-requirements :burialAssistance="$application" disabled="true" readonly="true" :files="$files"/>
                </div>
            </div>
        </div>
    </section>
    <div id="confirm-rejection" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ route('admin.applications.reject', ['id' => $application->id]) }}" method="post">
                @csrf
                <div class="modal-content">
                    <div class="modal-body">
                        <p>Are you sure you want to reject this application? This will not be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit">Confirm Rejection</button>
                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <x-applications-modal-loader :application_id="$application->id"/>
</div>
@endsection