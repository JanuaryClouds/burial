@props(['cheques'])
@php
    if (auth()->user()->hasRole('admin')) {
        $role = 'admin';
    } else if (auth()->user()->hasRole('superadmin')) {
        $role = 'superadmin';
    }
@endphp
@extends('layouts.stisla.' . $role)
<title>Cheqeues</title>
<meta name="csrf-token" content="{{ csrf_token() }}">
@section('content')
<div class="main-content">
    <section class="section">
        <div class="section-header d-flex justify-content-between align-items-center">
            <h1>Cheques</h1>
        </div>
    </section>
    <div>
        <x-filter-data-form type="cheques" :startDate="$startDate" :endDate="$endDate" />
        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4>Cheques Per Status</h4>
                    </div>
                    <div class="card-body">
                        @if (!$chequesPerStatus->isEmpty())
                            <canvas 
                                id="cheques-per-status"
                                data-chart-data='@json($chequesPerStatus->pluck('count'))'
                                data-chart-labels='@json($chequesPerStatus->pluck('name'))'
                                data-chart-type="bar"
                                data-empty="{{ $chequesPerStatus->isEmpty() ? 'true' : 'false' }}"
                            ></canvas>
                        @else
                            <p class="text-muted">No Data</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4>Details</h4>
            </div>
            <div class="card-body">
                @if(!$cheques->isEmpty())
                    <div class="table-responsive">
                        <div class="dataTables_wrapper container-fluid">
                            <table id="generic-table" class="table data-table" style="width:100%">
                                <thead>
                                    <tr role="row">
                                        @foreach ($cheques->first()->getAttributes() as $column => $value)
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
                                    @foreach ($cheques as $entry)
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
            <x-export-to-pdf-button :startDate="$startDate" :endDate="$endDate" type="cheques" />
        </div>
    </div>
</div>
@endsection