@extends('layouts.metronic.admin')
<title>{{ $client->first_name }} {{ $client->last_name }}'s Application</title>
@section('content')
    @include('client.partial.interview-alert')
    @include('client.partial.assessment-modal')
    @include('client.partial.schedule')
    @include('client.partial.services-modal')
    <div class="row mt-5">
        <form action="{{ route('client.update', $client) }}" method="post" id="contentForm">
            @csrf
            @method('put')
            @include('client.partial.create-form-body')
        </form>
    </div>
@endsection
