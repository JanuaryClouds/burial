@extends('layouts.app')
@section('content')
    <div class="d-flex flex-column gap-4">
        <div class="card">
            <form action="{{ route('beneficiary.family.update', ['familyId' => $family->id]) }}" method="post">
                @csrf
                @method('put')
                <div class="card-header">
                    <h4 class="card-title">
                        {{ $family->name }} Information
                    </h4>
                </div>
                <div class="card-body">
                    @include('beneficiary.family.partials.info', [
                        'family' => $family,
                        'readonly' => $readonly,
                    ])
                </div>
                @role('superadmin')
                    <div class="card-footer d-flex justify-content-end">
                        <button class="btn btn-warning btn-sm">
                            <i class="fas fa-save"></i>
                            Save Changes to Data
                        </button>
                    </div>
                @endrole
            </form>
        </div>
        <div class="row">
            <div class="col-6">
                <a href="{{ route('client.show', $family->client) }}" class="card px-6 py-5 hover-elevate-up">
                    <div class="d-flex gap-2 justify-content-start align-items-center">
                        <i class="fa-solid fa-info-circle"></i>
                        Client Tracking Number: <strong>{{ $family->client->tracking_no }}</strong>
                    </div>
                </a>
            </div>
            <div class="col-6">
                <a href="{{ route('beneficiary.show', ['id' => $family->client->beneficiary->id]) }}"
                    class="card px-6 py-5 hover-elevate-up">
                    <div class="d-flex gap-2 justify-content-start align-items-center">
                        <i class="fa-solid fa-users"></i>
                        Family Member of
                        <strong>{{ $family->client->beneficiary->fullname() }}</strong>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection
