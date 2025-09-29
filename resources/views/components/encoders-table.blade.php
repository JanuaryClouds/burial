@props(['encoders' => []])
<div class="card">
    <div class="card-header">
        <h4>Encoders</h4>
    </div>
    <div class="card-body">
        @if (count($encoders) == 0)
            <p class="text-center">No applications with encoders found.</p>
        @endif
        @foreach ($encoders as $encoder)
            @php
                $encodedApplications = App\Models\BurialAssistance::where('encoder', $encoder->id)->get();
            @endphp
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="d-flex align-items-baseline">
                    <h5>{{ $encoder->first_name }} {{ $encoder->last_name }}
                        <span class="badge badge-pill badge-primary ml-2">{{ $encodedApplications->count() }}</span>
                    </h5>
                </span>
                <button class="btn btn-primary" data-target="#encoder-{{ $encoder->id }}-applications" data-toggle="collapse" aria-expanded="false" aria-controls="encoder-{{ $encoder->id }}-applications">Show Encoded Applications</button>
            </div>
            <div id="encoder-{{ $encoder->id }}-applications" class="collapse">
                <div class="table-responsive">
                    <div class="dataTables_wrapper container-fluid">
                        <table id="applications-per-encoder-table" class="table data-table" style="width:100%">
                            <thead>
                                <tr role="row">
                                    <th class="sorting sort-handler">Tracking No.</th>
                                    <th class="sorting">Deceased</th>
                                    <th class="sorting">Claimant</th>
                                    <th class="sorting">Submitted on</th>
                                    @if (Request::is('admin/applications/history'))
                                        <th class="sorting">Status</th>
                                    @endif
                                    <th class="">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($encodedApplications as $application)
                                    <tr class="bg-white">
                                        <td>{{ $application->tracking_no }}</td>
                                        <td>
                                            {{ $application->deceased->first_name }}
                                            {{ $application->deceased->middle_name ? Str::charAt($application->deceased->middle_name, 0).'.' : '' }}
                                            {{ $application->deceased->last_name }}
                                            {{ $application->deceased->suffix }}
                                        </td>
                                        <td>
                                            {{ $application->claimant->first_name }}
                                            {{ $application->claimant->middle_name ? Str::charAt($application->claimant->middle_name, 0).'.' : '' }}
                                            {{ $application->claimant->last_name }}
                                            {{ $application->claimant->suffix }}
                                        </td>
                                        <td>{{ $application->application_date }}</td>
                                        @if (Request::is('admin/applications/history'))
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
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>