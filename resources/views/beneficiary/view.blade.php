@extends('layouts.app')
@section('content')
    <div class="d-flex flex-column gap-4">
        <div class="card">
            <form action="{{ route('beneficiary.update', $beneficiary->id) }}" method="post" id="contentForm">
                @csrf
                @method('put')
                <div class="card-header">
                    <h4 class="card-title">{{ $beneficiary->fullname() }}'s Information</h4>
                </div>
                <div class="card-body">
                    @include('client.partials.beneficiary-info', [
                        'client' => $beneficiary->client,
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
                <a href="{{ route('client.show', $beneficiary->client) }}" class="card px-6 py-5 hover-elevate-up">
                    <div class="d-flex gap-2 justify-content-start align-items-center">
                        <i class="fa-solid fa-info-circle"></i>
                        Client Tracking Number: <strong>{{ $beneficiary->client->tracking_no }}</strong>
                    </div>
                </a>
            </div>
        </div>
    </div>
@endsection
