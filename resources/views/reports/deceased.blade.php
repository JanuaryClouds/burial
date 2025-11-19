@props(['deceased'])
@extends('layouts.stisla.admin')
<title>Deceased</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header d-flex justify-content-between align-items-center">
            <h1>Deceased</h1>
        </div>
    </section>
    <div>
        <x-filter-data-form type="deceased" :startDate="$startDate" :endDate="$endDate" />
        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Deceased By Gender</h4>
                    </div>
                    <div class="card-body">
                        @if (!$deceasedPerGender->isEmpty())
                            <canvas 
                                id="deceased-per-gender"
                                data-chart-data='@json($deceasedPerGender->pluck('count'))'
                                data-chart-labels='@json($deceasedPerGender->pluck('name'))'
                                data-chart-type="bar"
                                data-empty="{{ $deceasedPerGender->isEmpty() ? 'true' : 'false' }}"
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
                                data-chart-data='@json($deceasedPerReligion->pluck('count'))'
                                data-chart-labels='@json($deceasedPerReligion->pluck('name'))'
                                data-chart-type="pie"
                                data-empty="{{ $deceasedPerReligion->isEmpty() ? 'true' : 'false' }}"
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
                                data-chart-data='@json($deceasedPerBarangay->pluck('count'))'
                                data-chart-labels='@json($deceasedPerBarangay->pluck('name'))'
                                data-chart-type="pie"
                                data-empty="{{ $deceasedPerBarangay->isEmpty() ? 'true' : 'false' }}"
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
                    <div class="table-responsive overflow-x-hidden">
                        <div class="dataTables_wrapper">
                            <table id="generic-table" class="table data-table generic-table" style="width:100%">
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
        <div class="d-flex justify-content-center">
            <x-export-to-pdf-button :startDate="$startDate" :endDate="$endDate" type="deceased" />
        </div>
    </div>
</div>
@endsection