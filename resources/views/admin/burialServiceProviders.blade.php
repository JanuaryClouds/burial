@extends('layouts.admin');
@section('content')
@section('breadcrumb')
<x-breadcrumb :items="[
    ['label' => 'Burial Service Providers', 'url' => route('admin.burial.providers')]
    ]" />
@endsection

<div class="flex flex-col gap-12">
    <header class="flex justify-between items-center">
        <h2 class="font-bold text-xl text-gray-700">Burial Service Providers</h2>

        <a href="{{ route('admin.burial.new.provider') }}" class="px-4 py-2 text-white font-semibold tracking-widest uppercase text-sm bg-gray-700 rounded-lg hover:bg-gray-300 hover:text-black transition-colors">
            <i class="fa-solid fa-plus"></i>
            New Burial Service Provider
        </a>
    </header>
    <x-burial-service-provider-table />
</div>

@endsection