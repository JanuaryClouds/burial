@extends('layouts.guest')
@section('content')
    <title>{{ $burialAssistance->deceased->first_name }} {{ $burialAssistance->deceased->last_name }} Burial Assistance</title>
    <div
        class="container d-flex min-vh-100 align-items-center justify-content-center m-5"
    >
        <div
            class="row w-100"
        >
            <div class="col-12 col-lg-8 mx-auto">
                <div
                    class="row d-flex flex-column justify-content-center align-items-center g-2 gap-4"
                    x-data="{ showApplication: false }"
                >
                    <div class="col">
                        <x-assistance-process-tracker :burialAssistance="$burialAssistance"/>
                    </div>
                    <div class="col">
                        <div
                            class="container bg-white shadow rounded p-4"
                        >
                            <div
                                class="row"
                            >
                                <div
                                    class="col d-flex gap-2 justify-content-end align-items-end"
                                >
                                    <x-change-claimant-modal :burialAssistance="$burialAssistance"/>
                                    <button
                                        class="btn btn-primary"
                                        x-on:click="showApplication = !showApplication"
                                        x-text="showApplication ? 'Hide Application' : 'Show Application'"
                                    >
                                    </button>
                                    <a
                                        name=""
                                        id=""
                                        class="btn btn-secondary"
                                        href="{{ route('landing.page') }}"
                                        role="button"
                                        >Back to Landing Page</a
                                    >
                                </div>
                            </div>
                        </div>
                    </div>
                    <div
                        class="col"
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
                    <div class="col"
                        x-show="showApplication"
                        x-transition=""
                    >
                        <x-deceased-form :deceased="$burialAssistance->deceased" disabled="true" readonly="true"/>
                    </div>
                    <div class="col"
                        x-show="showApplication"
                        x-transition=""
                    >
                        <x-claimant-form :claimant="$burialAssistance->claimant" disabled="true" readonly="true"/>
                    </div>
                    <div class="col"
                        x-show="showApplication"
                        x-transition=""
                    >
                        <x-burial-assistance-details-form :burialAssistance="$burialAssistance" disabled="true" readonly="true"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
@endsection