@extends('layouts.metronic.admin')
<title>{{ $page_title }}</title>
@section('content')
@include('admin.partial.cards')
<div class="card mt-8">
    <div class="card-header">
        <h1 class="card-title">Manage Funeral Assistances</h1>
    </div>
    <div class="card-body">
        @if ($data->count() > 0)
            <div class="table-responsive overflow-x-hidden">
                <div class="dataTables_wrapper">
                    <table id="{{ $resource }}-table" class="table data-table" style="width:100%">
                        <thead class="border-bottom border-bottom-1 border-gray-200 fw-bold">
                            <tr role="row">
                                @foreach ($data->first()->getAttributes() as $column => $value)
                                    @if (in_array($column, $renderColumns))
                                        @if ($column == 'client_id')
                                            <th class="sorting sort-handler">Name</th>
                                        @else
                                            <th class="">{{ str_replace('Id', '', str_replace('_', ' ', Str::title($column))) }}</th>
                                        @endif
                                    @endif
                                @endforeach
                                <th class="sorting sort-handler">
                                    Beneficiary
                                </th>
                                <th class="sorting sort-handler">
                                    Submitted at
                                </th>
                                <th class="sorting sort-handler">
                                    Status
                                </th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $entry)
                                @if ($entry->approved_at == null || $entry->forwarded_at == null)
                                    <tr class="">
                                        @foreach ($entry->getAttributes() as $key =>$value)
                                            @if (in_array($key, $renderColumns))
                                                @if ($key == 'client_id')
                                                    <td>
                                                        {{ $entry->client->first_name }} {{ Str::limit($entry->client->middle_name, '1', '.') }} {{ $entry->client->last_name }} {{ $entry->client->suffix ?? '' }}
                                                    </td>
                                                @endif
                                            @endif
                                        @endforeach
                                        <td>
                                            {{ $entry->client->beneficiary->first_name }} {{ Str::limit($entry->client->beneficiary->middle_name, '1', '.') }} {{ $entry->client->beneficiary->last_name }} {{ $entry->client->beneficiary->suffix ?? '' }}
                                        </td>
                                        <td>
                                            {{ $entry->client->created_at->format('M d, Y') }}
                                        </td>
                                        <td>
                                            @if ($entry->approved_at == null)
                                                <span class="badge badge-danger">Pending</span>
                                            @else
                                                @if ($entry->forwarded_at == null)
                                                    <span class="badge badge-warning">Approved</span>
                                                @else
                                                    <span class="badge badge-success">Forwarded</span>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('funeral-assistances.view', ['id' => $entry->id]) }}" class="btn btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="d-flex justify-content-center">
                <p class="text-muted">No Funeral Assistances</p>
            </div>
        @endif
    </div>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('#{{ $resource }}-table').DataTable({
            order: [[2, 'desc']],
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