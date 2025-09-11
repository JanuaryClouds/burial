@extends('layouts.stisla.admin')
<title>Pending Applications</title>
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>{{ Str::ucfirst($status) }} Applications</h1>
            </div>
            <div class="section-header-breadcrumb">

            </div>
        </section>

        <section class="section">
            <div class="table-responsive">
                <div class="dataTables_wrapper container-fluid dt-bootstrap4">
                    <table id="applications-table" class="table dataTable" style="width:100%">
                        <thead>
                            <tr role="row">
                                <th class="sorting sort-handler">Tracking No.</th>
                                <th class="sorting">Deceased</th>
                                <th class="sorting">Claimant</th>
                                <th class="sorting">Submitted</th>
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
                                    <td>
                                        {{ $application->claimant->first_name }}
                                        {{ $application->claimant->middle_name ? Str::charAt($application->claimant->middle_name, 0).'.' : '' }}
                                        {{ $application->claimant->last_name }}
                                        {{ $application->claimant->suffix }}
                                    </td>
                                    <td>{{ $application->application_date }}</td>
                                    <td><x-application-actions :application="$application" /></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

    </div>
@endsection