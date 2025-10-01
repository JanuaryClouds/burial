@props(['deceased'])
@extends('layouts.stisla.superadmin')
<title>Claimants</title>
@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Claimants</h1>
        </div>
    </section>
    <div>
        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Claimants of Burial Assistance per Barangay</h4>
                    </div>
                    <div class="card-body">
                        <canvas 
                            id="applicationsDistributions"
                            data-chart-data='@json($claimantsPerBarangay->pluck('count'))'
                            data-chart-labels='@json($claimantsPerBarangay->pluck('name'))'
                            data-chart-type="pie"
                        ></canvas>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Relationships to Deceased</h4>
                    </div>
                    <div class="card-body">
                        <canvas 
                            id="relationshipDistributions"
                            data-chart-data='@json($claimantsPerRelationship->pluck('count'))'
                            data-chart-labels='@json($claimantsPerRelationship->pluck('name'))'
                            data-chart-type="pie"
                        ></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4>Details</h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <div class="dataTables_wrapper container-fluid">
                        <table id="generic-table" class="table data-table" style="width:100%">
                            <thead>
                                <tr role="row">
                                    @foreach ($claimants->first()->getAttributes() as $column => $value)
                                        @php
                                            $excemptions = ['id', 'created_at', 'updated_at'];
                                        @endphp
                                        @if (!in_array($column, $excemptions))
                                            <th class="sorting sort-handler">{{ Str::title(Str::replace('_', ' ', $column)) }}</th>     
                                        @endif
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($claimants as $entry)
                                    <tr class="bg-white">
                                        @foreach ($entry->getAttributes() as $key => $value)
                                            @if (!in_array($key, $excemptions))
                                                @if ($key == 'barangay_id')
                                                    <td>{{ $entry->barangay->name }}</td>
                                                @elseif ($key == 'relationship_to_deceased')
                                                     <td>{{ $entry->relationship->name }}</td>
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
            </div>
        </div>
    </div>
</div>
@endsection