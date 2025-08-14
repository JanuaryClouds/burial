@extends('layouts.admin');
@section('content')
<title>New Burial Service</title>
<div class="container flex-column align-items-center gap-2">
    <!-- <h2 class="g-0 bg-white p-3 rounded shadow-sm w-100">New Burial Service</h2> -->

    <x-burial-service-form />
</div>

@endsection