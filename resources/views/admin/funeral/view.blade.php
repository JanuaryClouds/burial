@extends('layouts.stisla.admin')
<title>{{ $page_title }}</title>
@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <div class="d-flex w-100 justify-content-between align-items-center">
                <h1>{{ $page_title }}</h1>
                <span>
                    <a href="{{ route('clients.gis-form', ['id' => $data->client_id]) }}" class="btn btn-secondary mr-2" data-no-loader>
                        Generate GIS Form
                    </a>
                    <a href="{{ route('funeral-assistances.view.certificate', ['id' => $data->id]) }}" class="btn btn-secondary mr-2" target="_blank">
                        Download Certificate
                    </a>
                    @if ($data->approved_at == null)
                        <a href="{{ route('funeral-assistances.view.approved', ['id' => $data->id]) }}" class="btn btn-success mr-2">
                            <i class="fas fa-check-circle"></i>
                            Approved
                        </a>
                    @endif
                    @if (!$data->approved_at == null && $data->submitted_at == null)
                        <a href="{{ route('funeral-assistances.view.forwarded', ['id' => $data->id]) }}" class="btn btn-primary">
                            <i class="fas fa-forward"></i>
                            Forwarded
                        </a>
                    @endif
                </span>
            </div>
        </div>
    </section>
    <div class="section-body">
        @if ($data->approved_at == null)
            <div class="card">
                <div class="card-body">
                    @include('admin.funeral.partial.approval')
                </div>
            </div>
        @endif
        <div class="card">
            <div class="card-body">
                @include('client.partial.client-info', ['readonly' => true])
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                @include('client.partial.beneficiary-info', ['readonly' => true])
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                @include('client.partial.beneficiary-fam', ['readonly' => true])
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                @include('client.partial.documents', ['readonly' => true])
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                @include('client.partial.beneficiary-assessment', ['readonly' => true])
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                @include('client.partial.recommended-assistance', ['readonly' => true])
            </div>
        </div>
    </div>
</div>
@endsection