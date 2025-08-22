@extends('layouts.admin')
@section('content')
<title>Burial Service Providers</title>
<div class="container d-flex flex-column gap-2">
    <header class="d-flex justify-content-between gap-2 bg-white rounded p-3 shadow-sm">
        <h2 class="mb-0">Burial Service Providers</h2>
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
    <div class="bg-white p-3 rounded shadow-sm">
        <x-burial-service-provider-table />
    </div>
</div>

@endsection