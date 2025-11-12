@extends('layouts.stisla.admin')
<title>{{ $page_title }}</title>
@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <div class="d-flex w-100 justify-content-between align-itemsp-center">
                <h1>{{ $page_title }}</h1>
                <span>
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
                @include('client.partial.clientInfo', ['readonly' => true])
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                @include('client.partial.beneficiaryInfo', ['readonly' => true])
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                @include('client.partial.beneficiaryFam', ['readonly' => true])
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                @include('client.partial.documents', ['readonly' => true])
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                @include('client.partial.beneficiaryAssessment', ['readonly' => true])
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                @include('client.partial.recommendedAssistance', ['readonly' => true])
            </div>
        </div>
    </div>
</div>
@endsection