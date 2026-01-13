@extends('layouts.metronic.admin')
<title>CMS - {{ $resource }}</title>
@php
    $routeName = 'superadmin.cms.update';
    if ($resource === 'barangays') {
        $paramKey = 'id';
    } elseif ($resource === 'relationships') {
        $paramKey = 'id';
    } elseif ($resource === 'providers') {
        $paramKey = 'id';
    } elseif ($resource === 'services') {
        $paramKey = 'id';
    } else {
        $paramKey = 'uuid';
    }
@endphp

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h4 class="card-title">{{ Str::ucfirst($resource) }}s</h4>
        </div>
        <div class="card-body">
            @if ($data->isEmpty())
                <p class="text-muted text-center">
                    No Data
                </p>
            @else
                @include('cms.partials.datatable')
            @endif
        </div>
    </div>
@endsection
