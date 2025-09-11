@extends('layouts.stisla.guest')
@section('content')
<title>{{ $burialAssistance->deceased->first_name }} {{ $burialAssistance->deceased->last_name }} Burial Assistance</title>
<section class="section d-flex justify-content-center align-items-center min-vh-100 p-4">
    <div class="section-body d-flex align-items-center">
        <div
            class="row d-flex flex-column justify-content-center align-items-center"
            x-data="{ showApplication: false }"
        >
            <div class="col-sm-11 col-lg-10">
                <x-assistance-process-tracker :burialAssistance="$burialAssistance"/>
            </div>
            <div class="col-sm-11 col-lg-10 mt-4">
                <div
                    class="container bg-white shadow rounded p-4"
                >
                    <div
                        class="row"
                    >
                        <div
                            class="col d-flex justify-content-end align-items-end"
                        >
                            <span class="mr-2">
                                <button
                                    class="btn btn-primary"
                                    x-on:click="showApplication = !showApplication"
                                    x-text="showApplication ? 'Hide Application' : 'Show Application'"
                                >
                                </button>
                            </span>
                            <span class="mr-2">
                                <button class="btn btn-secondary" type="button" data-toggle="modal" data-target="#changeClaimantModal">Change Claimant</button>
                            </span>
                            <span class="mr-2">
                                <a
                                    name=""
                                    id=""
                                    class="btn btn-secondary"
                                    href="{{ route('landing.page') }}"
                                    role="button"
                                    >Back to Landing Page</a
                                >
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div
                class="col-sm-11 col-lg-10 mt-4"
                x-show="showApplication"
                x-transition=""
            >
                <div
                    class="container bg-white shadow rounded p-4"
                >
                    <div
                        class="row flex-column justify-content-center align-items-center g-2"
                    >
                        <div class="col">
                            <h1>Submitted Application</h1>
                        </div>
                        <div class="col">
                            <p>This is a copy of the burial assistance application. All fields in this copy are not editable and fixed. This allows you to track the progress of your application. Some text have to be hidden for privacy.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-11 col-lg-10"
                x-show="showApplication"
                x-transition=""
            >
                <x-deceased-form :deceased="$burialAssistance->deceased" disabled="true" readonly="true"/>
            </div>
            <div class="col-sm-11 col-lg-10"
                x-show="showApplication"
                x-transition=""
            >
                <x-claimant-form :claimant="$burialAssistance->claimant" disabled="true" readonly="true"/>
            </div>
            <div class="col-sm-11 col-lg-10"
                x-show="showApplication"
                x-transition=""
            >
                <x-burial-assistance-details-form :burialAssistance="$burialAssistance" disabled="true" readonly="true"/>
            </div>
        </div>
    </div>
</section>
<x-change-claimant-modal :burialAssistance="$burialAssistance"/>
    
@endsection