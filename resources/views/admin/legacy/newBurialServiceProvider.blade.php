@extends('layouts.admin');
@section('content')
<title>New Burial Service Provider</title>
<div class="d-flex flex-column gap-3">
    <!-- <h2 class="bg-white p-3 shadow-sm rounded">
        New Burial Service Provider
    </h2> -->

    <x-burial-service-provider-form />
</div>

@endsection