@extends('layouts.admin')
@section('content')
@section('breadcrumb')
<x-breadcrumb :items="[
    ['label' => 'Generate Burial Service Form', 'url' => route('admin.burial.requests')],
    ['label' => $approvedAssistanceRequest->deceased_firstname, 'url' => ''],
    ]"/>
@endsection
<title>Generate Burial Service Form</title>
<div class="flex flex-col gap-12">
    <h2 class="font-bold text-center text-xl text-gray-700">Generate Burial Service Form</h2>

    <x-burial-service-form-from-request :serviceRequest="$approvedAssistanceRequest" />
</div>

@endsection