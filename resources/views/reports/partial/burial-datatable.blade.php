@if (!$burialAssistances->isEmpty())
    <div class="card">
        <div class="card-body">
            <div class="table-responsive overflow-x-hidden">
            <div class="dataTables_wrapper">
            <table id="reports-applications-table" class="table data-table" style="width:100%">
                <thead class="border-bottom border-bottom-1 border-gray-200 fw-bold">
                    <tr role="row">
                        <th class="sorting sort-handler">Tracking No.</th>
                        <th class="sorting">Deceased</th>
                        <th class="sorting">Date of Death</th>
                        <th class="sorting">Claimant</th>
                        <th class="sorting">Submitted on</th>
                        <th>Last Update</th>
                        <th class="sorting">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($burialAssistances as $application)
                        <tr class="">
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
                            <td>
                                @if ($application->status === 'pending')
                                    <span class="badge badge-pill badge-warning">{{ ucfirst($application->status) }}</span>
                                @elseif ($application->status === 'processing')
                                    <span class="badge badge-pill badge-primary">{{ ucfirst($application->status) }}</span>
                                @elseif ($application->status === 'approved')
                                    <span class="badge badge-pill badge-success">{{ ucfirst($application->status) }}</span>
                                @elseif ($application->status === 'released')
                                    <span class="badge badge-pill badge-success">{{ ucfirst($application->status) }}</span>
                                @elseif ($application->status === 'rejected')
                                    <span class="badge badge-pill badge-danger">{{ ucfirst($application->status) }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            </div>
            </div>
        </div>
    </div>
@endif