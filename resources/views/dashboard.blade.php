@extends('layouts.metronic.admin')
@section('content')
    @php
        static $modalsLoaded = false;
    @endphp
    <title>Dashboard</title>
    @include('admin.partial.cards')
    <div class="row mt-5 mt-xl-8">
        <div class="col-12">
            <x-latest-clients-table />
        </div>
    </div>
    <x-applications-modal-loader />
@endsection
