@if ($logs->count() > 0)
    <div class="table-responsive overflow-x-hidden">
        <div class="dataTables_wrapper">
            <table id="activity-logs-table" class="table data-table" style="width:100%">
                <thead>
                    <tr role="row">
                        @foreach ($logs->first()->getAttributes() as $column => $value)
                            @php
                                $excemptions = ['id', 'updated_at', 'causer_type'];
                            @endphp
                            @if (!in_array($column, $excemptions))
                                @if ($column == 'causer_id')
                                    <th class="sorting sort-handler">Caused By</th>
                                @elseif ($column == 'created_at')
                                    <th class="sorting sort-handler">Date & Time</th>
                                @else
                                    <th class="">{{ Str::title(Str::replace('_', ' ', $column)) }}</th>
                                @endif
                            @endif
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($logs as $log)
                        <tr class="bg-white">
                            @foreach ($log->getAttributes() as $key => $value)
                                @if (!in_array($key, $excemptions))
                                    {{-- Since the permission to view logs can be assigned to more than just a superadmin, it is best to make it exclusive to the superadmin --}}
                                    @if (auth()->user()->hasRole('superadmin') && $key == 'causer_id')
                                        <td>{{ auth()->user()->where('id', $value)->first()->first_name ?? '' }}</td>
                                    @elseif ($key == 'properties')
                                        @php
                                            $props = $log->properties->toArray();
                                        @endphp
                                        <td>
                                            {{ $props['ip'] ?? 'N/A' }}
                                            <p class="text-muted">
                                                ({{ $props['browser'] ?? 'N/A' }})
                                            </p>
                                        </td>
                                    @else
                                        <td>{{ $value }}</td>
                                    @endif
                                @endif
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@else
    <div class="alert alert-info">No activity logs found.</div>
@endif
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('#activity-logs-table').DataTable({
            responsive: true,
            ordering: true,
            order: [
                [-1, 'desc']
            ],
            dom:
                // First row: buttons on the left, filter on the right
                "<'row mb-2'<'col-sm-6 d-flex align-items-center'l<'me-3'>B><'col-sm-6 d-flex justify-content-end'f>>" +
                // Table
                "<'row'<'col-12'tr>>" +
                // Bottom row: info and pagination
                "<'row mt-2'<'col-sm-6'i><'col-sm-6 d-flex justify-content-end'p>>",
            buttons: [{
                    extend: 'excel',
                    text: '<i class="me-2 fa fa-file-excel"></i>Export to Excel',
                    className: 'btn btn-primary py-1 px-3'
                },
                {
                    extend: 'print',
                    text: '<i class="me-2 fa fa-print"></i>Print',
                    className: 'btn btn-secondary py-1 px-3 ml-2'
                },
                // 'copy', 
                // 'csv', 
                // 'excel', 
                // 'pdf', 
                // 'print'
            ],
            classes: {
                sortAsc: '', // override ascending class
                sortDesc: '', // override descending class
                sortable: '' // override neutral sortable class 
            }
        });
    })
</script>
