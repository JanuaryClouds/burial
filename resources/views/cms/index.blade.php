@extends('layouts.dashboard')
@section('content')

@section('breadcrumb')
<x-breadcrumb :items="[
        ['label' => $page_title, 'url' => route(Auth::user()->getRoleNames()->first() . '.' . $resource . '.index')],
    ]" />
@endsection

<div class="flex justify-between mb-5 overflow-auto">
    <h1 class="text-3xl font-bold mb-2 text-center text-gray-800">{{ $page_title }} records</h1>
    <div x-data="{ showModal: false }">
        <button @click="showModal = true"
            class="px-5 py-2 text-white bg-[#1A4798] rounded-lg hover:bg-[#F4C027] hover:text-black hover:border border-[#F4C027] transition-colors">
            <i class="fa-solid fa-plus"></i> Add {{ $resource }}
        </button>
        @include('cms.create')
    </div>
</div>
@include('components.alert')
<div class="w-full bg-white p-8 rounded-lg shadow-lg border border-gray-200 overflow-auto max-h-[75vh]">
    <table class="min-w-full border border-gray-200 shadow-lg" id="{{ $resource }}-table">
        <thead class="bg-[#1A4798]">
            <tr class="text-white uppercase text-md leading-normal">
                @foreach ($columns as $column)
                <th class="py-3 px-4 cursor-pointer">
                    {{ $column }}
                </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="text-gray-600 text-sm font-normal text-center">
            @foreach ($data as $record)
            <tr class="border border-gray-200 hover:bg-gray-100 transition-colors">
                <td class="py-1 px-4">{{ $record->id }}</td>
                <td class="py-1 px-4">{{ $record->name }}</td>
                @if($resource === 'role' || $resource === 'permission')
                <td class="py-1 px-4">{{ $record->guard_name }}</td>
                @else
                <td class="py-1 px-4">{{ $record->remarks }}</td>
                @endif
                <td class="py-1 px-4">
                    <div class="inline-flex items-center space-x-2">
                        <div x-data="{ showEditModal: false }">
                            <button @click="showEditModal = true"
                                class="inline-block p-2 bg-blue-100 text-blue-500 hover:bg-blue-200 hover:text-blue-700 rounded transition-colors"
                                title="Edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </button>
                            @include('cms.edit')
                        </div>
                        <div x-data="{ showDeleteModal: false }">
                            <button @click="showDeleteModal = true"
                                class="inline-block p-2 bg-red-100 text-red-500 hover:bg-red-200 hover:text-red-700 rounded transition-colors"
                                title="Delete">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                            @include('cms.delete')
                        </div>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<script>
$(document).ready(function() {
    $('#{{ $resource }}-table').DataTable({
        processing: true,
        serverSide: false,
        pageLength: 10,
        order: [
            [0, 'desc']
        ],

        dom: '<"flex justify-between items-center mb-4"lf>rt<"flex justify-between items-center mt-4"ip>',

        initComplete: function() {
            const table = this.api();

            const $searchInput = $('div.dataTables_filter input');
            $searchInput.addClass(
                'ml-2 px-4 py-1 border border-gray-300 rounded focus:outline-none ' +
                'focus:ring focus:ring-[#1A4798] focus:ring-opacity-50'
            );

            const $lengthSelect = $('div.dataTables_length select');
            $lengthSelect.addClass(
                'px-4 py-1 my-2 border border-gray-300 rounded focus:outline-none ' +
                'focus:ring focus:ring-[#1A4798] focus:ring-opacity-50'
            );
        },

        drawCallback: function(settings) {
            const $paginateButtons = $('div.dataTables_paginate .paginate_button');
            $paginateButtons.addClass(
                'px-4 py-2 text-black rounded-lg hover:bg-[#1A4798]/20 disabled:opacity-50 ' +
                'disabled:cursor-not-allowed transition-colors'
            );

            const $currentPage = $('div.dataTables_paginate .paginate_button.current');
            $currentPage.removeClass('bg-gray-700 text-white');
            $currentPage.addClass(
                'bg-[#1A4798] text-white px-4 m-2 py-2 rounded-lg transition-colors hover:bg-[#1A4798]/90'
            );
        }
    });
});
</script>

@endsection