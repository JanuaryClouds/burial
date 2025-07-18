@extends('layouts.admin');
@section('content')

<div class="flex flex-col gap-12">
    <form action="" method="post">
        <h2 class="font-bold text-center text-xl text-gray-700">New Burial Service</h2>
    </form>

    <x-burial-service-form />
</div>

@endsection