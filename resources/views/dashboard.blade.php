@extends('layouts.metronic.admin')
@section('content')
@php 
    static $modalsLoaded = false; 
@endphp
<title>Dashboard</title>
@include('admin.partial.cards')
<!-- TODO: Add assignment -->
@cannot('manage-assignments')
    <div class="row mt-5 mt-xl-8">
        <div class="col-12">
            <x-assigned-applications-list />
        </div>
    </div>
@endcan
<div class="row mt-5 mt-xl-8">
    <div class="col-12">
        <x-latest-clients-table />
    </div>
</div>        
<x-applications-modal-loader />

@endsection