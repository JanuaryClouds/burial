@extends('layouts.admin');
@section('content')
<title>New Burial Service</title>
<div class="flex flex-col gap-12">
    <h2 class="font-bold text-center text-xl text-gray-700">New Burial Service</h2>

    <x-burial-service-form />
</div>

@endsection