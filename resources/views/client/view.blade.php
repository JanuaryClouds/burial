@extends('layouts.app')
@section('content')
    <div class="d-flex flex-column gap-4">
        @if (auth()->check() && auth()->user()->roles()->count() > 0)
            @include('client.partials.menu')
            @include('client.partials.assessment-modal')
            @include('client.partials.schedule')
            @include('client.partials.services-modal')
            @include('client.partials.referral-modal')
        @endif
        @include('client.partials.interview-alert')
        <div class="row">
            @can('manage-content')
                <form action="{{ route('client.update', $client) }}" method="post" id="contentForm">
                    @csrf
                    @method('put')
                    @include('client.partials.create-form-body')
                </form>
            @endcan
            @cannot('manage-content')
                @include('client.partials.create-form-body')
            @endcannot
        </div>
    </div>
@endsection
