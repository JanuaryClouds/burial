<div class="section-body">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between gap-3">
                @if (Request::is('applications/all'))
                    <div class="form-group w-100 mr-2">
                        <label for="filter-status">Filter by Status</label>
                        <select name="status" id="status" class="custom-select w-100">
                            <option value="all">All</option>
                            @foreach ($statusOptions as $option)
                                <option value="{{ $option }}">{{ Str::title($option) }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
                <div class="form-group w-100 mr-2">
                    <label for="min-date">Submitted on/after</label>
                    <input id="min-date" class="form-control" type="date" name="min-date">
                </div>
                <div class="form-group w-100 mr-2">
                    <label for="max-date">Submitted on/before</label>
                    <input id="max-date" class="form-control" type="date" name="max-date">
                </div>
                <div class="form-group w-100 mr-2">
                    <label for="filter-barangay">Filter by Barangay</label>
                    <select name="filter-barangay" id="filter-barangay" class="form-control w-100">
                        <option value="all">All</option>
                        @foreach ($barangays as $key => $barangay)
                            <option value="{{ $barangay }}">{{ $barangay }}</option>
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
                <div class="dataTables_wrapper">
                    <table id="applications-table" class="table data-table" style="width:100%">
                        <thead class="border-bottom border-bottom-1 border-gray-200 fw-bold">
                            <tr role="row">
                                <th class="sorting">Tracking No.</th>
                                <th class="sorting">Deceased</th>
                                <th class="sorting">Date of Death</th>
                                <th class="sorting">Claimant</th>
                                <th class="sorting sort-handler">Submitted on</th>
                                <th>Last Update</th>
                                @if (Route::is('burial.index'))
                                    <th class="sorting">Status</th>
                                @endif
                                <th class="">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($applications as $application)
                                <tr class="">
                                    <td>{{ $application->tracking_no }}</td>
                                    <td>
                                        {{ $application->deceased->first_name }}
                                        {{ $application->deceased->middle_name ? Str::charAt($application->deceased->middle_name, 0) . '.' : '' }}
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
                                                @else
                                                    {{ $application->claimant->first_name }}
                                                    {{ $application->claimant->middle_name ? Str::limit($application->claimant->middle_name, 1, '.') : '' }}
                                                    {{ $application->claimant->last_name }}
                                                    {{ $application->claimant->suffix }}
                                                    <p class="text-muted">
                                                        ({{ $application->claimant->barangay->name }})
                                                    </p>
                                                @endif
                                            @endforeach
                                        @else
                                            {{ $application->claimant->first_name }}
                                            {{ $application->claimant->middle_name ? Str::limit($application->claimant->middle_name, 1, '.') : '' }}
                                            {{ $application->claimant->last_name }}
                                            {{ $application->claimant->suffix }}
                                            <p class="text-muted">({{ $application->claimant->barangay->name }})</p>
                                        @endif
                                    </td>
                                    <td>{{ $application->application_date }}</td>
                                    <td>
                                        {{ $application->processLogs->last()->date_in ?? 'Submitted on: ' . $application->application_date }}
                                        <p class="text-muted">
                                            {{ $application->processLogs->count() > 1 ? '(' . $application->processLogs->last()->loggable?->description . ')' : '' }}
                                        </p>
                                    </td>
                                    @if (Route::is('burial.index'))
                                        <td>
                                            @if ($application->status === 'pending')
                                                <span
                                                    class="badge badge-pill badge-warning">{{ ucfirst($application->status) }}</span>
                                            @elseif ($application->status === 'processing')
                                                <span
                                                    class="badge badge-pill badge-primary">{{ ucfirst($application->status) }}</span>
                                            @elseif ($application->status === 'approved')
                                                <span
                                                    class="badge badge-pill badge-success">{{ ucfirst($application->status) }}</span>
                                            @elseif ($application->status === 'released')
                                                <span
                                                    class="badge badge-pill badge-success">{{ ucfirst($application->status) }}</span>
                                            @elseif ($application->status === 'rejected')
                                                <span
                                                    class="badge badge-pill badge-danger">{{ ucfirst($application->status) }}</span>
                                            @endif
                                        </td>
                                    @endif
                                    <td><x-application-actions :application="$application" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
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
            order: [
                [4, 'asc']
            ],
            dom:
                // First row: buttons on the left, filter on the right
                "<'row mb-2'<'col-sm-6 d-flex align-items-center'l<'mr-3'>><'col-sm-6 d-flex justify-content-end align-items-center'f<'me-3'>B>>" +
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
                    text: 'Excel',
                    className: 'btn btn-success py-1 px-3',
                    action: function() {
                        const url = window.location.href;
                        const status = url.substring(url.lastIndexOf('/') + 1);
                        window.location.href = '/applications/export'
                    }
                }
            ],
            columnDefs: [{
                    orderable: false,
                    targets: [7]
                } // disable sorting on the Actions column
            ],
            classes: {
                sortAsc: '', // override ascending class
                sortDesc: '', // override descending class
                sortable: '' // override neutral sortable class
            }
        });;

        maxDate.value = sessionStorage.getItem('maxDate') || '';
        minDate.value = sessionStorage.getItem('minDate') || '';
        if (statusSelect) {
            statusSelect.value = sessionStorage.getItem('status') || 'all';
        }
        barangaySelect.value = sessionStorage.getItem('barangay') || 'all';
        filterTable();

        resetBtn.addEventListener('click', () => {
            if (statusSelect) {
                statusSelect.value = 'all';
            }
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

        if (statusSelect) {
            statusSelect.addEventListener('change', () => {
                filterTable();
            });
        }

        function filterTable() {
            const status = statusSelect ? statusSelect.value : 'all';
            const min = minDate.value;
            const max = maxDate.value;

            sessionStorage.setItem('minDate', min);
            sessionStorage.setItem('maxDate', max);
            sessionStorage.setItem('status', status);
            sessionStorage.setItem('barangay', barangaySelect.value);

            table.rows().every(function() {
                const data = this.data();
                const dateText = data[table.column(':contains("Submitted on")').index()];
                const statusText = table.column(':contains("Status")').index() !== undefined ?
                    data[table.column(':contains("Status")').index()].toLowerCase() :
                    '';
                const deceasedBarangay = data[table.column(':contains("Deceased")').index()];
                const claimantBarangay = data[table.column(':contains("Claimant")').index()];

                const date = dateText.trim();

                const dateOk = (!min || date >= min) && (!max || date <= max);
                const statusOk = (status === 'all') || statusText.includes(status.toLowerCase());
                const barangayOk = (barangaySelect.value === 'all') || deceasedBarangay.includes(
                    barangaySelect.value) || claimantBarangay.includes(barangaySelect.value);

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
