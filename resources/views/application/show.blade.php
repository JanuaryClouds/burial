@extends('layouts.app')
@section('content')
    <div class="d-flex flex-column gap-4">
        @role('staff')
            @include('application.partials.menu')
        @endrole
        {{-- TODO add timeline --}}
        <div class="card multicolor-border">
            <div class="card-body">
                @include('client.partials.client-info', [
                    'readonly' => true,
                    'client' => $application->client,
                ])
            </div>
        </div>
        {{-- TODO add beneficiary info --}}
        {{-- TODO add beneficiary family --}}
        {{-- TODO add submitted documents --}}
        @role('staff')
            {{-- TODO add interview history --}}
            {{-- TODO add assessment info --}}
            {{-- TODO add recommendation info --}}
        @endrole
    </div>
@endsection
