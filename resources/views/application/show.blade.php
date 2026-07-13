@extends('layouts.app')
@section('content')
    @role('staff')
        {{-- TODO add menu toolbar --}}
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
@endsection
