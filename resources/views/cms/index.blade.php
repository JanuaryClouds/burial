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
            @if (!isset($data) || $data->count() == 0)
                <p class="text-muted text-center">
                    No Data
                </p>
            @else
                @include('partials.datatable.index', [
                    'columns' => $columns,
                    'classes' => 'with-actions',
                ])
            @endif
        </div>
    </div>
    @can('manage-content')
        @if (Route::has(str_replace('.index', '', Route::currentRouteName()) . '.store'))
            <div id="newContent" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="newContent" aria-hidden="true">
                <form action="{{ route(str_replace('.index', '', Route::currentRouteName()) . '.store') }}" method="post">
                    @csrf
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Add New {{ ucfirst($resource) }}</h5>
                                <button class="btn btn-icon btn-sm btn-active-icon-primary" type="button"
                                    data-bs-dismiss="modal" aria-label="Close">
                                    <i class="ki-duotone ki-cross fs-1">
                                        <span class="path1"></span><span class="path2"></span>
                                    </i>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="d-flex flex-column gap-2">
                                    @foreach ($columns as $data)
                                        @foreach ($data as $field)
                                            <div class="form-group">
                                                <label
                                                    for="{{ $field }}">{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                                                <input type="text" name="{{ $field }}" id="{{ $field }}"
                                                    class="form-control">
                                            </div>
                                        @endforeach
                                    @endforeach
                                    @if (Route::getCurrentRoute()->getName() === 'role.index')
                                        @include('cms.partials.create-role')
                                    @endif
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-light" type="button" data-bs-dismiss="modal">
                                    Cancel
                                </button>
                                <button class="btn btn-primary" type="submit">
                                    Save
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        @endif
    @endcan
@endsection
