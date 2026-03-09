@extends('layouts.metronic.admin')
<title>{{ $page_title }}</title>
@section('content')
    @include('admin.partial.cards')
    <div class="card mt-8">
        <div class="card-header">
            <h2 class="card-title fs-2 fw-medium">Manage {{ $resource }}</h2>
        </div>
        <div class="card-body">
            @if (!isset($data) || $data->count() == 0)
                <p class="text-muted text-center">
                    No Data.
                </p>
            @else
                @include('partials.datatable.index', [
                    'data' => $data,
                    'resource' => $resource,
                    'columns' => $columns,
                    'route' => Request::route()->getName(),
                ])
            @endif
        </div>
    </div>
    <script nonce="{{ $nonce ?? '' }}">
        document.addEventListener('DOMContentLoaded', function() {
            $('#{{ $resource }}-table').DataTable({
                responsive: true,
                ordering: true,
                dom:
                    // First row: buttons on the left, filter on the right
                    "<'row mb-2'<'col-sm-6 d-flex align-items-center'l><'col-sm-6 d-flex justify-content-end'f<'me-5'>B>>" +
                    // Table
                    "<'row'<'col-12'tr>>" +
                    // Bottom row: info and pagination
                    "<'row mt-2'<'col-sm-6'i><'col-sm-6 d-flex justify-content-end'p>>",
                buttons: [{
                        extend: 'excel',
                        text: 'Export to Excel',
                        className: 'btn btn-primary py-1 px-3',
                    },
                    {
                        extend: 'print',
                        text: 'Print',
                        className: 'btn btn-secondary py-1 px-3 ml-2',
                    },
                    // 'copy', 
                    // 'csv', 
                    // 'pdf',
                    // 'print'
                ],
                classes: {
                    sortAsc: '', // override ascending class
                    sortDesc: '', // override descending class
                    sortable: '' // override neutral sortable class 
                }
            });
        });
    </script>
@endsection
