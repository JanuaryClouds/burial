<div class="card">
    <div class="card-header">
        <h2 class="card-title fw-medium fs-2">Latest Clients</h2>
    </div>
    <div class="card-body">
        @if (isset($data) && $data->isEmpty())
            <p class="text-muted text-center">No Data</p>
        @else
            <div class="table-responsive overflow-x-hidden">
                <div class="dataTables_wrapper">
                    <table id="latest-clients-table" class="table data-table">
                        <thead class="border-bottom border-bottom-1 border-gray-200 fs-7 fw-bold">
                            <tr role="row" class="text-uppercase">
                                <th>Tracking No.</th>
                                <th>Name</th>
                                <th>Address</th>
                                <th>Contact No.</th>
                                <th>Status</th>
                                <th class="sorting sort-handler">Submitted on</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $entry)
                                <tr class="border-bottom border-bottom-1 border-gray-200">
                                    <td>{{ $entry->tracking_no }}</td>
                                    <td>
                                        {{ $entry->first_name }}
                                        {{ $entry->middle_name ? Str::charAt($entry->middle_name, 0) . '.' : '' }}
                                        {{ $entry->last_name }} {{ $entry->suffix }}
                                    </td>
                                    <td>
                                        {{ $entry->house_no . ' ' . $entry->street . ', ' . $entry->barangay->name }}
                                    </td>
                                    <td>{{ $entry->contact_no }}</td>
                                    <td>
                                        @if ($entry->claimant)
                                            <span class="badge badge-pill badge-info">
                                                <a href="{{ route('burial.show', $entry->claimant->burialAssistance->id) }}"
                                                    class="text-white">
                                                    Burial
                                                </a>
                                            </span>
                                        @elseif ($entry->funeralAssistance)
                                            <span class="badge badge-pill badge-info">
                                                <a href="{{ route('funeral.show', $entry->funeralAssistance->id) }}"
                                                    class="text-white">
                                                    Funeral
                                                </a>
                                            </span>
                                        @endif
                                        @if (!$entry->interviews->isEmpty())
                                            @if (Carbon\Carbon::parse($entry->interviews->first()->schedule)->diffInHours(Carbon\Carbon::now()) >= 1)
                                                <span class="badge badge-pill badge-danger">
                                                    Interview
                                                </span>
                                            @elseif (Carbon\Carbon::parse($entry->interviews->first()->schedule)->diffInHours(Carbon\Carbon::now()) < 1)
                                                <span class="badge badge-pill badge-warning">
                                                    Interview
                                                </span>
                                            @endif
                                        @elseif (!$entry->assessment->isEmpty())
                                            <span class="badge badge-pill badge-info">
                                                Assessed
                                            </span>
                                        @else
                                            <span class="badge badge-pill badge-warning">
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        {{ Carbon\Carbon::parse($entry->created_at)->format('M d, Y') }}
                                    </td>
                                    <td>
                                        <a href="{{ route('client.show', $entry) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye pe-0"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('#latest-clients-table').DataTable({
            responsive: true,
            ordering: true,
            order: [
                [5, 'desc']
            ], // order by Submitted on column descending
            dom:
                // First row: buttons on the left, filter on the right
                "<'row mb-2'<'col-sm-6 d-flex align-items-center'i<'mr-3'>><'col-sm-6 d-flex justify-content-end'f>>" +
                // Table
                "<'row'<'col-12'tr>>",
            // Bottom row: info
            // "<'row mt-2'<'col-sm-6'i>>"
            buttons: [{
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
                sortAsc: '', // override ascending class
                sortDesc: '', // override descending class
                sortable: '' // override neutral sortable class 
            }
        });
    });
</script>
