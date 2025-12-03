@extends('layouts.metronic.guest')

@section('content')
    <title>{{ $burialAssistance->deceased->first_name }} {{ $burialAssistance->deceased->last_name }} Burial Assistance
    </title>
    <div class="container-xxl min-vh-100 my-10">
        <div class="d-flex flex-column gap-4">
            <x-assistance-process-tracker :burialAssistance="$burialAssistance" :updateAverage="$updateAverage" />
            @include('burial.partials.application-copy')
        </div>
    </div>
@endsection
