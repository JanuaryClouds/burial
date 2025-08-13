@extends('layouts.admin')
@section('content')
@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Burial Requests', 'url' => route('admin.burial.requests')]
        ]"/>
@endsection

    <title>Burial Requests</title>

    <div
        class="container-fluid flex-column"
    >
        <div
            class="row justify-content-start d-flex flex-column g-2"
        >
            <div class="col d-flex justify-content-between gap-2">
                <h3>Burial Requests</h3>
                <a
                    name=""
                    id=""
                    class="btn btn-outline-primary d-flex align-items-center gap-2"
                    href="{{ route('admin.burial.request.xlsx') }}"
                    role="button"
                    ><i class="fa-solid fa-file-excel"></i>Export to XLSX</a
                >
            </div>
            <div class="col">
                <x-burial-assistance-requests-table />
            </div>
        </div>        
    </div>

@endsection