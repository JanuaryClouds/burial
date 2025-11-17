@extends('layouts.metronic.admin')
@section('content')
@php 
    static $modalsLoaded = false; 
@endphp
<title>Dashboard</title>
@include('admin.partial.dashboard-cards')
@can('manage-assignments')
    <div class="row">
        <div class="col-12 col-md-12 col-lg-12">
            <x-assigned-applications-list />
        </div>
    </div>
@endcan
<div class="row mt-5 mt-xl-8">
    <div class="col-12">
        <x-assigned-applications-list />
    </div>
</div>
<div class="row mt-5 mt-xl-8">
    <div class="col-12">
        <x-latest-clients-table />
    </div>
</div>        
<x-applications-modal-loader />

@endsection