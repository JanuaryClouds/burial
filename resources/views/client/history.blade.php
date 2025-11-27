@extends('layouts.metronic.guest')
@section('content')
    <div class="container-xxl min-vh-100 my-10">
        <div class="d-flex flex-column gap-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Interview History</h4>
                </div>
                <div class="card-body">
                    @include('client.partial.interviews')
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Burial Assistances</h4>
                </div>
                <div class="card-body">
                    {{-- TODO: List of Burial Assistances --}}
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Burial Assistances</h4>
                </div>
                <div class="card-body">
                    {{-- TODO: List of Funeral Assistances --}}
                </div>
            </div>
        </div>
    </div>
@endsection
