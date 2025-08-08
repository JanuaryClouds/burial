@extends('layouts.admin');
@section('content')
<title>New Burial Service</title>
<div class="container flex-column align-items-center gap-2">
    <h2 class="fw-bold text-center">New Burial Service</h2>

    <x-burial-service-form />
</div>

@endsection