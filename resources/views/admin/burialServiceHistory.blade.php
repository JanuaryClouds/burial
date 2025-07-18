@extends('layouts.admin');
@section('content')

<div class="flex flex-col gap-12">
    <header class="flex justify-between items-center">
        <h2 class="font-bold text-xl text-gray-700">Burial Service History</h2>

        <!-- TODO: Either redirect to a different page or show a modal to input a new burial service -->
        <a href="{{ route('admin.burial.new') }}" class="px-4 py-2 text-white font-semibold tracking-widest uppercase text-sm bg-gray-700 rounded-lg hover:bg-gray-300 hover:text-black transition-colors">
            New Burial Service
        </a>
    </header>
    <x-burial-service-history-table />
</div>


@endsection