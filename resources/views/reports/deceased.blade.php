@props(['deceased'])
@extends('layouts.stisla.superadmin')
<title>Deceased</title>
@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Deceased</h1>
        </div>
    </section>
    <div>
        <div class="row">
            <div class="col-12 col-lg-6" x-data="{ period: 'month'}">
                <div class="card">
                    <div class="card-header">
                        <h4>Applied Deceased per Period</h4>
                        <span class="card-header-action">
                            <select name="" id="" class="form-control" x-on:change="period = $event.target.value">
                                <option value="month">Month</option>
                                <option value="week">Week</option>
                                <option value="today">Today</option>
                            </select>
                        </span>
                    </div>
                    <div class="card-body">
                        <div
                            x-show="period == 'month'"
                            x-cloak
                            x-transition
                        >
                            @if (!$deceasedThisMonth->isEmpty())
                                <canvas 
                                    id="applications-month"
                                    data-chart-data='@json($deceasedThisMonth->pluck('count'))'
                                    data-chart-labels='@json($deceasedThisMonth->pluck('period'))'
                                    data-chart-type="bar"
                                ></canvas>
                            @else
                                <p class="text-muted">No Data</p>
                            @endif
                        </div>
                        <div
                            x-show="period == 'week'"
                            x-cloak
                            x-transition
                        >
                            @if (!$deceasedThisWeek->isEmpty())
                                <canvas 
                                    id="applications-week"
                                    data-chart-data='@json($deceasedThisWeek->pluck('count'))'
                                    data-chart-labels='@json($deceasedThisWeek->pluck('period'))'
                                    data-chart-type="bar"
                                ></canvas>
                            @else
                                <p class="text-muted">No Data</p>
                            @endif
                        </div>
                        <div
                            x-show="period == 'today'"
                            x-cloak
                            x-transition
                        >
                            @if (!$deceasedThisDay->isEmpty())
                                <canvas 
                                    id="application-today"
                                    data-chart-data='@json($deceasedThisDay->pluck('count'))'
                                    data-chart-labels='@json($deceasedThisDay->pluck('period'))'
                                    data-chart-type="bar"
                                ></canvas>
                            @else
                                <p class="text-muted">No Data</p>
                            @endif
                        </div>
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
                            ></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="mb-2">
            <div class="card">
                <div class="card-header">
                    <h4>Deceased Per Barangay</h4>
                </div>
                <div class="card-body">
                    <x-per-barangay-table />
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
            </div>
        </div>
    </div>
</div>
@endsection