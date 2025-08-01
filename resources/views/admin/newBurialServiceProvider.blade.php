@extends('layouts.admin');
@section('content')
<title>New Burial Service Provider</title>
<div class="flex flex-col gap-12">
    <h2 class="font-bold text-center text-xl text-gray-700">
        New Burial Service Provider
    </h2>

    <x-burial-service-provider-form />
</div>

@endsection