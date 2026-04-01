@extends('layouts.metronic.admin')
@section('content')
    @include('client.partials.menu')
    @include('client.partials.interview-alert')
    @include('client.partials.assessment-modal')
    @include('client.partials.schedule')
    @include('client.partials.services-modal')
    <div class="row mt-5">
        <form action="{{ route('client.update', $client) }}" method="post" id="contentForm">
            @csrf
            @method('put')
            @include('client.partials.create-form-body')
        </form>
    </div>
@endsection
