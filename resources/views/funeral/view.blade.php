@extends('layouts.metronic.admin')
<title>{{ $page_title }}</title>
@section('content')
    @include('client.partial.create-form-body')
    @if ($data->approved_at == null)
        <div class="card">
            <div class="card-body">
                @include('funeral.partials.approval')
            </div>
        </div>
    @endif
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
@endsection
