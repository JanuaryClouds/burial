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
@endsection