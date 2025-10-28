@extends('layouts.stisla.admin')
<title>{{ Str::ucfirst($status) }} Applications</title>
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ Str::ucfirst($status) }} Applications</h1>
            </div>
            <div class="section-header-button">
            </div>
        </section>
        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4>{{ Str::ucfirst($status) }} Applications</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <div class="dataTables_wrapper container-fluid">
                            <table id="applications-table" class="table data-table" style="width:100%">
                                <thead>
                                    <tr role="row">
                                        <th class="sorting sort-handler">Tracking No.</th>
                                        <th class="sorting">Deceased</th>
                                        <th class="sorting">Date of Death</th>
                                        <th class="sorting">Claimant</th>
                                        <th class="sorting">Funeraria</th>
                                        <th class="">Amount</th>
                                        <th class="sorting">Submitted on</th>
                                        @if (Request::is('admin/applications/history'))
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
                                            </td>
                                            <td>{{ $application->deceased->date_of_death }}</td>
                                            <td>
                                                @if ($application?->claimantChanges->isNotEmpty())
                                                    @foreach ($application->claimantChanges as $cc)
                                                        @if ($cc->status === 'approved')
                                                            {{ $cc->newClaimant->first_name }} {{ Str::limit($cc->newClaimant->middle_name, 1, '.') }} {{ $cc->newClaimant->last_name }} {{ $cc?->newClaimant->suffix }}
                                                        @endif
                                                    @endforeach
                                                @else
                                                    {{ $application->claimant->first_name }}
                                                    {{ $application->claimant->middle_name ? Str::limit($application->claimant->middle_name, 1, '.') : ''}}
                                                    {{ $application->claimant->last_name }}
                                                    {{ $application->claimant->suffix }}
                                                @endif
                                            </td>
                                            <td>{{ $application->funeraria }}</td>
                                            <td>{{ $application->amount }}</td>
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
@endsection