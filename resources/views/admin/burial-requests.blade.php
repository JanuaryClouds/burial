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
            <div class="col">
                <h3>Burial Requests</h3>
            </div>
            <div class="col">
                <x-burial-assistance-requests-table />
            </div>
        </div>        
    </div>

@endsection