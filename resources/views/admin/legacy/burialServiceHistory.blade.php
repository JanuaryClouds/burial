@extends('layouts.admin')
@section('content')
<title>Burial Service History</title>
<div class="d-flex flex-column justify-content-start gap-4 p-0">
    <header class="d-flex justify-content-between gap-2 p-3 bg-white rounded shadow-sm">
        <h2 class="d-flex mb-0">Burial Service History</h2>
        <span class="d-flex gap-2">
            <a
                name=""
                id=""
                class="btn btn-outline-primary d-flex align-items-center gap-2"
                href="{{ route('admin.burial.service.xlsx') }}"
                role="button"
                target="_blank"
                ><i class="fa-solid fa-file-excel"></i>Export All to XLSX</a
            >
            <a href="{{ route('admin.burial.new') }}" class="btn btn-primary d-flex gap-2 align-items-center">
                <i class="fa-solid fa-plus"></i>
                New Burial Service
            </a>
        </span>
    </header>

    <div class="bg-white p-3 rounded shadow-sm">
        <x-burial-service-history-table />
    </div>
</div>

@endsection