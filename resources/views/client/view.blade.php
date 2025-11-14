@extends('layouts.stisla.admin')
<title>{{ $page_title }}</title>
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header d-flex justify-content-between">
                <h1>{{ $client->first_name }} {{ $client->last_name }}'s application</h1>
                <div class="d-flex justify-content-center">
                    <a href="{{ route('clients.gis-form', ['id' => $client->id]) }}" class="btn btn-info mr-2">
                        Generate GIS Form
                    </a>
                    @if ($client->interviews->count() == 0)
                        <button class="btn btn-primary mr-2" type="button" data-toggle="modal" data-target="#set-schedule-modal">
                            Schedule an Interview
                        </button>
                    @endif
                        <button class="btn btn-secondary mr-2" type="button" data-toggle="modal" data-target="#assessment-modal">
                            Assessment
                        </button>
                    @if ($client->assessment->count() > 0)
                        <button class="btn btn-success" type="button" data-toggle="modal" data-target="#services-modal">
                            Services
                        </button>
                    @endif
                </div>
            </div>
        </section>
        @include('client.partial.interview-alert')
        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4>Client Information</h4>
                </div>
                <div class="card-body">
                    @include('client.partial.clientInfo')
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4>Beneficiary Information</h4>
                </div>
                <div class="card-body">
                    @include('client.partial.beneficiaryInfo')
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4>Beneficiary Family Composition</h4>
                </div>
                <div class="card-body">
                    @include('client.partial.beneficiaryFam')
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h4>Submitted Documents</h4>
                </div>
                <div class="card-body">
                    @include('client.partial.documents')
                </div>
            </div>
            @if ($client->assessment->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h4>Assessment</h4>
                    </div>
                    <div class="card-body">
                        @include('client.partial.beneficiaryAssessment')
                    </div>
                </div>
            @endif
            @if ($client->recommendation->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <h4>Recommendation</h4>
                    </div>
                    <div class="card-body">
                        @include('client.partial.recommendedAssistance')
                    </div>
                </div>
            @endif
        </div>
        @include('client.partial.schedule')
        @include('client.partial.assessment-modal')
        @include('client.partial.services-modal')
    </div>
@endsection