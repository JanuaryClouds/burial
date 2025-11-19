<div class="card">
    <div class="card-header">
        <h2 class="card-title fw-medium fs-2">Latest Applications</h2>
    </div>
    <div class="card-body">
        <div class="table-responsive overflow-x-hidden">
            <div class="dataTables_wrapper">
                <table id="latest-applications-table" class="table data-table">
                    <thead class="border-bottom border-bottom-1 border-gray-200 fs-7 fw-bold">
                        <tr role="row" class="text-uppercase">
                            <th>Tracking No.</th>
                            <th>Deceased</th>
                            <th>Date of Death</th>
                            <th>Claimant</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th class="sorting sort-handler">Submitted on</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($applications as $application)
                            <tr class="border-bottom border-bottom-1 border-gray-200">
                                <td>{{ $application->tracking_no }}</td>
                                <td>
                                    {{ $application->deceased->first_name }} {{ $application->deceased->middle_name ? Str::charAt($application->deceased->middle_name, 0).'.' : '' }} {{ $application->deceased->last_name }} {{ $application->deceased->suffix }}
                                </td>
                                <td>{{ $application->deceased->date_of_death }}</td>
                                <td>
                                    {{ $application->claimant->first_name }} {{ $application->claimant->middle_name ? Str::charAt($application->claimant->middle_name, 0).'.' : '' }} {{ $application->claimant->last_name }} {{ $application->claimant->suffix }}
                                </td>
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
                                <td>{{ $application->amount }}</td>
                                <td>{{ $application->application_date }}</td>
                                <td><x-application-actions :application="$application" /></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>