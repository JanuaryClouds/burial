@extends('layouts.app')
@section('content')
    <div class="d-flex flex-column gap-4">
        @if (auth()->check() && auth()->user()->hasRole('staff'))
            @include('client.partials.menu')
            @include('client.partials.assessment-modal')
            @include('client.partials.schedule')
            @include('client.partials.services-modal')
            @include('client.partials.referral-modal')
        @endif
        @include('client.partials.interview-alert')
        <form action="{{ route('client.update', $client->id) }}" method="post">
            @csrf
            @method('put')
            <div class="card">
                <div class="card-body">
                    @include('client.partials.client-info', [
                        'client' => $client,
                        'readonly' => $readonly,
                    ])
                </div>
                @role('superadmin')
                    <div class="card-footer d-flex justify-content-end">
                        <button class="btn btn-warning btn-sm" id="saveContentBtn">
                            <i class="fas fa-save"></i>
                            Save Changes to Data
                        </button>
                    </div>
                @endrole
            </div>
        </form>
        <div class="card">
            <div class="card-body">
                @include('client.partials.beneficiary-info', [
                    'client' => $client,
                    'readonly' => true,
                ])
            </div>
            @role('superadmin')
                <div class="card-footer d-flex justify-content-end">
                    <a name="" id="" class="btn btn-warning btn-sm"
                        href="{{ route('beneficiary.show', $client->beneficiary->id) }}" role="button">Edit Data</a>
                </div>
            @endrole
        </div>
        <div class="card">
            <div class="card-body d-flex flex-column gap-2">
                <h5 class="card-title">III. BENEFICIARY'S FAMILY COMPOSITION</h5>
                @foreach ($client->family as $family)
                    @include('beneficiary.family.partials.info', [
                        'family' => $family,
                        'readonly' => true,
                    ])
                    <hr>
                @endforeach
            </div>
        </div>
        @if ($client->assessment->count() > 0)
            <div class="card">
                <div class="card-body">
                    @include('client.partials.beneficiary-assessment', [
                        'client' => $client,
                    ])
                </div>
            </div>
        @endif
        @if ($client->recommendation->count() > 0)
            <div class="card">
                <div class="card-body">
                    @include('client.partials.recommended-assistance', [
                        'client' => $client,
                    ])
                </div>
            </div>
        @endif
        @if ($client->referral)
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">REFERRAL</h5>
                    @include('components.form-input', [
                        'name' => 'referral_to',
                        'label' => 'Referral To',
                        'type' => 'text',
                        'readonly' => true,
                        'value' => $client->referral->referral_to,
                    ])
                    @include('components.form-textarea', [
                        'name' => 'remarks',
                        'label' => 'Remarks',
                        'readonly' => true,
                        'value' => $client->referral->remarks,
                    ])
                </div>
            </div>
        @endif
        <div class="card">
            <div class="card-body">
                @include('client.partials.documents', [
                    'client' => $client,
                ])
            </div>
        </div>
    </div>
@endsection
