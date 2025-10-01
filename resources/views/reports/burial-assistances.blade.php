@extends('layouts.stisla.superadmin')
<title>Burial Assistances</title>
@section('content')
<div class="main-content">
    <div class="section">
        <div class="section-header">
            <h1>Burial Assistances Report</h1>
        </div>
    </div>
    <div class="">
        <div class="row">
            @foreach ($statistics as $statistic)
                <div class="col-12">
                    <x-card-stats :statistics="$statistic"/>
                </div>
            @endforeach
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                    <div class="table-responsive">
                        <div class="dataTables_wrapper container-fluid">
                            <table id="applications-table" class="table data-table" style="width:100%">
                                <thead>
                                    <tr role="row">
                                        <th class="sorting sort-handler">Tracking No.</th>
                                        <th class="sorting">Deceased</th>
                                        <th class="sorting">Claimant</th>
                                        <th class="sorting">Submitted on</th>
                                        <th class="sorting">Status</th>
                                        @if (!Request::is('superadmin/reports/*'))
                                            <th class="">Actions</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($burialAssistances as $application)
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
                                            @if (!Request::is('superadmin/reports/*'))
                                                <td><x-application-actions :application="$application" /></td>
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
    </div>
</div>
@endsection