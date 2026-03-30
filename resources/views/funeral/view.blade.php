@extends('layouts.metronic.admin')
<title>{{ $page_title }}</title>
@section('content')
    <form action="{{ route('funeral.update', $data->id) }}" method="post" id="contentForm">
        @csrf
        @method('PUT')
        <div class="d-flex flex-column gap-3">
            @include('client.partials.create-form-body')
            <div class="card">
                <div class="card-body">
                    @include('funeral.partials.approval')
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    @include('client.partials.beneficiary-assessment', ['readonly' => true])
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    @include('client.partials.recommended-assistance', ['readonly' => true])
                </div>
            </div>
        </div>
    </form>
@endsection
