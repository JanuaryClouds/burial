@extends('layouts.metronic.admin')
<title>{{ $page_title }}</title>
@section('content')
@include('admin.partial.cards')
<div class="card mt-8">
    <div class="card-header">
        <h2 class="card-title fs-2 fw-medium">Manage {{ $resource }}</h2>
    </div>
    <div class="card-body">
        <div class="table-responsive overflow-x-hidden">
            <div class="dataTables_wrapper">
                <table id="{{ $resource }}-table" class="table data-table" style="width:100%">
                    <thead class="border-bottom border-bottom-1 border-gray-200 fw-bold">
                        <tr role="row">
                            @foreach ($data->first()->getAttributes() as $column => $value)
                                @if (in_array($column, $renderColumns))
                                    @if (($column == 'first_name'))
                                        <th class="sorting sort-handler">Name</th>
                                    @else
                                        <th class="sorting sort-handler">{{ str_replace('Id', '', str_replace('_', ' ', Str::title($column))) }}</th>
                                    @endif
                                @endif
                            @endforeach
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $entry)
                            @if (!$entry->hasApplication())
                                <tr class="">
                                    @foreach ($entry->getAttributes() as $key =>$value)
                                        @if (in_array($key, $renderColumns))
                                            @if (($key == 'first_name'))
                                                <td>
                                                    {{ $entry->first_name }} {{ Str::limit($entry->middle_name, '1', '.') }} {{ $entry->last_name }} {{ $entry?->suffix }}
                                                </td>
                                            @elseif (($key == 'barangay_id'))
                                                <td>{{ $entry->barangay->name }}</td>
                                            @else
                                                <td>{{ $value }}</td>
                                            @endif
                                        @endif
                                    @endforeach
                                    <td>
                                        <a href="{{ route('clients.view', ['id' => $entry->id]) }}" class="btn btn-primary">
                                            <i class="fas fa-eye pe-0"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('#{{ $resource }}-table').DataTable({
            responsive: true,
            ordering: true,
            dom: 
                // First row: buttons on the left, filter on the right
                "<'row mb-2'<'col-sm-6 d-flex align-items-center'l<'me-5'>B><'col-sm-6 d-flex justify-content-end'f>>" +
                // Table
                "<'row'<'col-12'tr>>" +
                // Bottom row: info and pagination
                "<'row mt-2'<'col-sm-6'i><'col-sm-6 d-flex justify-content-end'p>>",
            buttons:[
                {
                    extend: 'excel',
                    text: '<i class="mr-2 fas fa-file-excel"></i> Export to Excel',
                    className: 'btn btn-primary py-1 px-3',
                },
                {
                    extend: 'print',
                    text: '<i class="mr-2 fas fa-print"></i> Print',
                    className: 'btn btn-secondary py-1 px-3 ml-2',
                },
                // 'copy', 
                // 'csv', 
                // 'pdf',
                // 'print'
            ],
            classes: {
                sortAsc: '',     // override ascending class
                sortDesc: '',    // override descending class
                sortable: ''     // override neutral sortable class 
            }
        });
    });
</script>
@endsection