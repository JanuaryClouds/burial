@extends('layouts.stisla.guest')

@section('content')
<title>{{ $burialAssistance->deceased->first_name }} {{ $burialAssistance->deceased->last_name }} Burial Assistance</title>

<div class="container min-vh-100 d-flex align-items-center justify-content-center p-4">
    <div class="row w-100" 
         x-data="{ showApplication: false, showClaimantChangeRequest: false }">
        <div class="col-12 col-lg-10 mx-auto mb-4">
            <x-assistance-process-tracker :burialAssistance="$burialAssistance"/>
        </div>
        <div class="col-12 col-lg-10 mx-auto mb-4">
            <div class="bg-white shadow rounded p-4">
                <div class="d-flex justify-content-end flex-wrap gap-2">
                    <button
                        class="btn btn-primary mr-2"
                        x-on:click="showApplication = !showApplication"
                        x-text="showApplication ? 'Hide Application' : 'Show Application'">
                    </button>

                    @if ($burialAssistance->status === 'pending' || $burialAssistance->status === 'processing')
                        @if ($burialAssistance->claimantChanges->count() == 0 && $burialAssistance->processLogs->last->loggable->order_no >= 3)
                            <button class="btn btn-secondary mr-2"
                                    type="button" 
                                    data-toggle="modal" 
                                    data-target="#changeClaimantModal">
                                Change Claimant
                            </button>
                        @else
                            <div class="btn-group mr-2" role="group" aria-label="Claimant group">
                                <button 
                                    class="btn btn-secondary disabled" disabled 
                                    @if ($burialAssistance->claimantChanges->last()->status == 'pending')
                                        title="Request for change of claimant is pending"
                                    @elseif ($burialAssistance->claimantChanges->last()->status == 'approved')
                                        title="A change of claimants can only be done once"
                                    @else
                                        title="Request for change of claimant has been denied"
                                    @endif
                                >
                                    Change Claimant
                                </button>
                                <button class="btn btn-secondary"
                                        x-on:click="showClaimantChangeRequest = !showClaimantChangeRequest">
                                    <i class="fas fa-eye" x-show="!showClaimantChangeRequest" x-cloak></i>
                                    <i class="fas fa-eye-slash" x-show="showClaimantChangeRequest" x-cloak></i>
                                </button>
                            </div>
                        @endif
                    @endif

                    <a href="{{ route('landing.page') }}" class="btn btn-secondary">
                        <i class="fas fa-home"></i>
                        Back to Landing Page
                    </a>
                </div>
            </div>
        </div>

        <template x-if="showApplication">
            <div class="col-12 col-lg-10 mx-auto mb-4">
                <div class="bg-white shadow rounded p-4 mb-4 text-center">
                    <h1>Submitted Application</h1>
                    <p>
                        This is a copy of the burial assistance application. All fields in this copy are not editable and fixed. 
                        This allows you to track the progress of your application. Some text have to be hidden for privacy.
                    </p>
                </div>

                <x-swa-form :application="$burialAssistance" :disabled="true" :readonly="true" class="mb-4"/>
                <x-deceased-form :deceased="$burialAssistance->deceased" disabled="true" readonly="true" class="mb-4"/>
                <x-claimant-form :claimant="$burialAssistance->claimant" disabled="true" readonly="true" class="mb-4"/>
                <x-burial-assistance-details-form :burialAssistance="$burialAssistance" disabled="true" readonly="true"/>
            </div>
        </template>
        <template x-if="showClaimantChangeRequest">
            <div class="col-12 col-lg-10 mx-auto mb-4">
                <x-claimant-form :claimant="$burialAssistance->claimantChanges->last()->newclaimant" disabled="true" readonly="true"/>
            </div>
        </template>
    </div>
</div>

<x-change-claimant-modal :burialAssistance="$burialAssistance"/>

@endsection
