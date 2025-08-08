@extends('layouts.admin');
@section('content')
@section('breadcrumb')
<x-breadcrumb :items="[
    ['label' => 'Burial History', 'url' => route('admin.burial.history')]
    ]" />
@endsection
<title>Burial Service History</title>
<div class="container d-flex flex-column gap-5">
    <header class="d-flex justify-content-between gap-2">
        <h2 class="d-flex fw-bold">Burial Service History</h2>
        <a href="{{ route('admin.burial.new') }}" class="btn btn-primary d-flex gap-2 align-items-center">
            <i class="fa-solid fa-plus"></i>
            New Burial Service
        </a>
    </header>

    <x-burial-service-history-table />
</div>

@endsection