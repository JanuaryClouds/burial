@extends('layouts.admin')
@section('content')
@section('breadcrumb')
    <x-breadcrumb :items="[
        ['label' => 'Burial Requests', 'url' => route('admin.burial.requests')]
        ]"/>
@endsection

    <title>Burial Requests</title>
    <x-burial-assistance-requests-table />

@endsection