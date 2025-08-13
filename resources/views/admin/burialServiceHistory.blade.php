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
        <span class="d-flex gap-2">
            <a
                name=""
                id=""
                class="btn btn-outline-primary d-flex align-items-center gap-2"
                href="{{ route('admin.burial.service.xlsx') }}"
                role="button"
                target="_blank"
                ><i class="fa-solid fa-file-excel"></i>Export to XLSX</a
            >
            <a href="{{ route('admin.burial.new') }}" class="btn btn-primary d-flex gap-2 align-items-center">
                <i class="fa-solid fa-plus"></i>
                New Burial Service
            </a>
        </span>
    </header>

    <x-burial-service-history-table />
</div>

@endsection