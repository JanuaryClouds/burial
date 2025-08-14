@extends('layouts.admin')
@section('content')
<title>Burial Requests</title>
<div
    class="d-flex flex-column gap-2"
>
    <div class="col d-flex justify-content-between gap-2 bg-white p-3 shadow-sm rounded">
        <h2 class="mb-0">Burial Requests</h2>
        <a
            name=""
            id=""
            class="btn btn-outline-primary d-flex align-items-center gap-2"
            href="{{ route('admin.burial.request.xlsx') }}"
            role="button"
            ><i class="fa-solid fa-file-excel"></i>Export All to XLSX</a
        >
    </div>
    <div class="col bg-white p-3 shadow-sm rounded">
        <x-burial-assistance-requests-table />
    </div>
</div>        

@endsection