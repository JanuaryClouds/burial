@extends('layouts.dashboard')
@section('content')

@section('breadcrumb')
<x-breadcrumb :items="[
        ['label' => 'Client', 'url' => route(Auth::user()->getRoleNames()->first() . '.' . $resource . '.index')],
        ['label' => $page_title],
    ]" />
@endsection

<div class="flex justify-between mb-5 overflow-auto">
    <h1 class="text-3xl font-bold mb-2 text-center text-gray-800">{{ $page_title }}</h1>
    <a href="{{ route(Auth::user()->getRoleNames()->first() . '.client.create') }}">
        <button @click="showModal = true"
            class="px-5 py-2 text-white bg-[#1A4798] rounded-lg hover:bg-[#F4C027] hover:text-black hover:border border-[#F4C027] transition-colors">
            <i class="fa-solid fa-plus"></i> Add {{ $resource }}
        </button>
    </a>
</div>
@include('components.alert')
<div class="">

</div>

@endsection