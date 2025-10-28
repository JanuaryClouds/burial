@extends('layouts.stisla.admin')
<title>Burial Assistance Applications</title>
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Burial Assistance Applications</h1>
            </div>
            <div class="section-header-button">
            </div>
        </section>
        <div class="section-body">
            <div class="card">
                <!-- <div class="card-header">
                    <h4>{{ Str::ucfirst($status) }} Applications</h4>
                </div> -->
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-12 col-md-4 col-lg-3">
                            <label for="filter-status">Filter by Status</label>
                            <select name="status" id="status" class="custom-select w-100">
                                <option value="all">All</option>
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="approved">Approved</option>
                                <option value="released">Released</option>
                                <option value="rejected">Rejected</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-8 col-lg-6">
                            <div class="row">
                                <div class="form-group col">
                                    <label for="min-date">Start Date</label>
                                    <input id="min-date" class="form-control" type="date" name="min-date">
                                </div>
                                <div class="form-group col">
                                    <label for="max-date">End Date</label>
                                    <input id="max-date" class="form-control" type="date" name="max-date">
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-12 col-md-4 col-lg-3">
                            <label for="filter-barangay">Filter by Barangay</label>
                            <select name="filter-barangay" id="filter-barangay" class="custom-select w-100">
                                <option value="all">All</option>
                                @foreach ($barangays as $b)
                                    <option value="{{ $b->name }}">{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button id="reset-filter" class="btn btn-outline-dark">
                            <i class="fas fa-rotate"></i>
                            Reset Filters
                        </button>
                    </div>

                    <div class="table-responsive mt-4">
                        <div class="dataTables_wrapper container-fluid">
                            <table id="applications-table" class="table data-table" style="width:100%">
                                <thead>
                                    <tr role="row">
                                        <th class="sorting">Tracking No.</th>
                                        <th class="sorting">Deceased</th>
                                        <th class="sorting">Date of Death</th>
                                        <th class="sorting">Claimant</th>
                                        <th class="sorting sort-handler">Submitted on</th>
                                        <th>Last Update</th>
                                        @if (Request::is('admin/applications'))
                                            <th class="sorting">Status</th>
                                        @endif
                                        <th class="">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($applications as $application)
                                        <tr class="bg-white">
                                            <td>{{ $application->tracking_no }}</td>
                                            <td>
                                                {{ $application->deceased->first_name }}
                                                {{ $application->deceased->middle_name ? Str::charAt($application->deceased->middle_name, 0).'.' : '' }}
                                                {{ $application->deceased->last_name }}
                                                {{ $application->deceased->suffix }}
                                                <p class="text-muted">({{ $application->deceased->barangay->name }})</p>
                                            </td>
                                            <td>{{ $application->deceased->date_of_death }}</td>
                                            <td>
                                                @if ($application?->claimantChanges->isNotEmpty())
                                                    @foreach ($application->claimantChanges as $cc)
                                                        @if ($cc->status === 'approved')
                                                            {{ $cc->newClaimant->first_name }} 
                                                            {{ Str::limit($cc->newClaimant->middle_name, 1, '.') }} 
                                                            {{ $cc->newClaimant->last_name }} 
                                                            {{ $cc?->newClaimant->suffix }}
                                                            <p class="text-muted">({{ $cc->newClaimant->barangay->name }})</p>
                                                        @endif
                                                    @endforeach
                                                @else
                                                    {{ $application->claimant->first_name }}
                                                    {{ $application->claimant->middle_name ? Str::limit($application->claimant->middle_name, 1, '.') : ''}}
                                                    {{ $application->claimant->last_name }}
                                                    {{ $application->claimant->suffix }}
                                                    <p class="text-muted">({{ $application->claimant->barangay->name }})</p>
                                                @endif
                                            </td>
                                            <td>{{ $application->application_date }}</td>
                                            <td>{{ $application->processLogs->last()->date_in ?? "Submitted on: " . $application->application_date }}</td>
                                            @if (Request::is('admin/applications'))
                                                <td>
                                                    @if ($application->status === 'pending')
                                                        <span class="badge badge-pill badge-primary">{{ ucfirst($application->status) }}</span>
                                                    @elseif ($application->status === 'processing')
                                                        <span class="badge badge-pill badge-secondary">{{ ucfirst($application->status) }}</span>
                                                    @elseif ($application->status === 'approved')
                                                        <span class="badge badge-pill badge-success">{{ ucfirst($application->status) }}</span>
                                                    @elseif ($application->status === 'released')
                                                        <span class="badge badge-pill badge-success">{{ ucfirst($application->status) }}</span>
                                                    @elseif ($application->status === 'rejected')
                                                        <span class="badge badge-pill badge-danger">{{ ucfirst($application->status) }}</span>
                                                    @endif
                                                </td>
                                            @endif
                                            <td><x-application-actions :application="$application" /></td>
                                            @if (auth()->user()->isAdmin())
                                                <div id="confirm-rejection-{{ $application->id }}" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                                                    <div class="modal-dialog" role="document">
                                                        <form action="{{ route('admin.applications.reject', ['id' => $application->id]) }}" method="post">
                                                            @csrf
                                                            <div class="modal-content">
                                                                <div class="modal-body">
                                                                    <p>Are you sure you want to reject this application? This will not be undone.</p>
                                                                </div>
                                                                <div class="modal-footer">
                                                                    <button class="btn btn-primary" type="submit">Confirm Rejection</button>
                                                                    <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancel</button>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@foreach ($applications as $application)
    <x-reject-modal :application="$application" />
    <x-process-updater :application="$application"/>
@endforeach
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const statusSelect = document.getElementById('status');
        const barangaySelect = document.getElementById('filter-barangay');
        const minDate = document.getElementById('min-date');
        const maxDate = document.getElementById('max-date');
        const resetBtn = document.getElementById('reset-filter');
        const table = $('#applications-table').DataTable({
            responsive: true,
            ordering: true, // keep ordering functional
            order: [[4, 'asc']],
            dom:
                // First row: buttons on the left, filter on the right
                "<'row mb-2'<'col-sm-6 d-flex align-items-center'l<'mr-3'>><'col-sm-6 d-flex justify-content-end align-items-center'f<'ml-3'>B>>" +
                // Table
                "<'row'<'col-12'tr>>" +
                // Bottom row: info and pagination
                "<'row mt-2'<'col-sm-6'><'col-sm-6 d-flex justify-content-end'p>>",
            buttons: [
                // 'copy',
                // 'csv',
                // 'excel',
                // 'pdf',
                // 'print'
                {
                    text: '<i class="mr-2 fas fa-file-excel"></i> Export to Excel',
                    className: 'btn btn-success py-1 px-3',
                    action: function() {
                        const url = window.location.href;
                        const status = url.substring(url.lastIndexOf('/') + 1);
                        window.location.href = '/applications/export'
                    }
                }
            ],
            columnDefs: [
                { orderable: false, targets: [7] } // disable sorting on the Actions column
            ],
            classes: {
                sortAsc: '',     // override ascending class
                sortDesc: '',    // override descending class
                sortable: ''     // override neutral sortable class
            }
        });;

        maxDate.value = sessionStorage.getItem('maxDate') || '';
        minDate.value = sessionStorage.getItem('minDate') || '';
        statusSelect.value = sessionStorage.getItem('status') || 'all';
        barangaySelect.value = sessionStorage.getItem('barangay') || 'all';
        filterTable();

        resetBtn.addEventListener('click', () => {
            statusSelect.value = 'all';
            barangaySelect.value = 'all';
            minDate.value = '';
            maxDate.value = '';
            filterTable();
        });
        
        minDate.addEventListener('change', () => {
            maxDate.min = minDate.value;
            filterTable();
        });

        maxDate.addEventListener('change', () => {
            minDate.max = maxDate.value;
            filterTable();
        });

        barangaySelect.addEventListener('change', () => {
            filterTable();
        });

        statusSelect.addEventListener('change', () => {
            filterTable();
        });

        function filterTable() {
            const status = statusSelect.value;
            const min = minDate.value;
            const max = maxDate.value;

            sessionStorage.setItem('minDate', min);
            sessionStorage.setItem('maxDate', max);
            sessionStorage.setItem('status', status);
            sessionStorage.setItem('barangay', barangaySelect.value);

            table.rows().every(function() {
                const data = this.data();
                const dateText = data[table.column(':contains("Submitted on")').index()];
                const statusText = table.column(':contains("Status")').index() !== undefined 
                    ? data[table.column(':contains("Status")').index()].toLowerCase()
                    : '';
                const deceasedBarangay = data[table.column(':contains("Deceased")').index()];
                const claimantBarangay = data[table.column(':contains("Claimant")').index()];

                const date = dateText.trim();

                const dateOk = (!min || date >= min) && (!max || date <= max);
                const statusOk = (status === 'all') || statusText.includes(status.toLowerCase());
                const barangayOk = (barangaySelect.value === 'all') || deceasedBarangay.includes(barangaySelect.value) || claimantBarangay.includes(barangaySelect.value);

                // Show/hide row manually
                if (dateOk && statusOk && barangayOk) {
                    $(this.node()).show();
                } else {
                    $(this.node()).hide();
                }
            });
        }
    });
</script>
@endsection