@extends('layouts.admin');
@section('content')
@section('breadcrumb')
<x-breadcrumb :items="[
    ['label' => 'Burial Service Providers', 'url' => route('admin.burial.providers')]
    ]" />
@endsection
<title>Burial Service Providers</title>
<div class="container d-flex flex-column gap-5">
    <header class="d-flex justify-content-between gap-2">
        <h2 class="fw-bold">Burial Service Providers</h2>
        <span class="d-flex gap-2">
            <a
                name=""
                id=""
                class="btn btn-outline-primary d-flex align-items-center gap-2"
                href="{{ route('admin.burial.provider.xlsx') }}"
                role="button"
                ><i class="fa-solid fa-file-excel"></i>Export All to XLSX</a>
            <a href="{{ route('admin.burial.new.provider') }}" class="btn btn-primary d-flex gap-2 align-items-center">
                <i class="fa-solid fa-plus"></i>
                New Burial Service Provider
            </a>
        </span>
    </header>
    <x-burial-service-provider-table />
</div>

@endsection