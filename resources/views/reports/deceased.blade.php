@props(['deceased'])
@extends('layouts.stisla.superadmin')
<title>Deceased</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header d-flex justify-content-between align-items-center">
            <h1>Deceased</h1>
            <span class="section-header-button">
                <x-export-to-pdf-button type="deceased" :startDate="$startDate" :endDate="$endDate" />
            </span>
        </div>
    </section>
    <div>
        <x-filter-data-form type="deceased" />
        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Deceased By Gender</h4>
                    </div>
                    <div class="card-body">
                        @if (!$deceasedByGender->isEmpty())
                            <canvas 
                                id="deceased-per-gender"
                                data-chart-data='@json($deceasedByGender->pluck('count'))'
                                data-chart-labels='@json($deceasedByGender->pluck('name'))'
                                data-chart-type="bar"
                                data-empty="{{ $deceasedByGender->isEmpty() ? 'true' : 'false' }}"
                            ></canvas>
                        @else
                            <p class="text-muted">No Data</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Deceased Per Religion</h4>
                    </div>
                    <div class="card-body">
                        <div>
                            <canvas
                                id="deceased-per-religion"
                                data-chart-data='@json($deceasedByReligion->pluck('count'))'
                                data-chart-labels='@json($deceasedByReligion->pluck('name'))'
                                data-chart-type="pie"
                                data-empty="{{ $deceasedByReligion->isEmpty() ? 'true' : 'false' }}"
                            ></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Applied Deceased per Period</h4>
                    </div>
                    <div class="card-body">
                        @if (!$deceasedThisMonth->isEmpty())
                            <canvas 
                                id="deceased-per-month"
                                data-chart-data='@json($deceasedThisMonth->pluck('count'))'
                                data-chart-labels='@json($deceasedThisMonth->pluck('period'))'
                                data-chart-type="bar"
                                data-empty="{{ $deceasedThisMonth->isEmpty() ? 'true' : 'false' }}"
                            ></canvas>
                        @else
                            <p class="text-muted">No Data</p>
                        @endif
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Deceased Per Barangay</h4>
                    </div>
                    <div class="card-body">
                        <div>
                            <canvas
                                id="deceased-per-barangay"
                                data-chart-data='@json($deceasedByBarangay->pluck('count'))'
                                data-chart-labels='@json($deceasedByBarangay->pluck('name'))'
                                data-chart-type="pie"
                                data-empty="{{ $deceasedByBarangay->isEmpty() ? 'true' : 'false' }}"
                            ></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4>Details</h4>
            </div>
            <div class="card-body">
                @if(!$deceased->isEmpty())
                    <div class="table-responsive">
                        <div class="dataTables_wrapper container-fluid">
                            <table id="generic-table" class="table data-table" style="width:100%">
                                <thead>
                                    <tr role="row">
                                        @foreach ($deceased->first()->getAttributes() as $column => $value)
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
                                    @foreach ($deceased as $entry)
                                        <tr class="bg-white">
                                            @foreach ($entry->getAttributes() as $key => $value)
                                                @if (!in_array($key, $excemptions))
                                                    @if ($key == 'gender')
                                                        <td>{{ $value == 1 ? 'Male' : 'Female' }}</td>
                                                    @elseif ($key == 'barangay_id')
                                                        <td>{{ $entry->barangay->name }}</td>
                                                    @elseif ($key == 'religion_id')
                                                        <td>{{ $entry->religion->name }}</td>
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
                @endif
            </div>
        </div>
    </div>
</div>
@endsection