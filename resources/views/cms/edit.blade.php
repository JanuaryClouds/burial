@extends('layouts.metronic.admin')
<title>{{ $page_title }}</title>
@section('content')
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">{{ isset($data->name) ? $data?->name : ucfirst($resource) }}</h4>
        </div>
        <form action="{{ route($resource . '.update', [$resource => $data]) }}" method="post" id="editForm">
            @csrf
            @method('PUT')
            <div class="card-body">
                <div class="d-flex flex-column gap-3">
                    @foreach ($data->getAttributes() as $field => $value)
                        <div class="row">
                            @if (!in_array($field, ['id', 'created_at', 'updated_at', 'is_active']))
                                <div class="col">
                                    @include('components.form-input', [
                                        'name' => $field,
                                        'value' => $value,
                                        'type' => 'text',
                                        'label' => Str::replace('_', ' ', ucfirst($field)),
                                    ])
                                </div>
                            @endif
                        </div>
                    @endforeach
                    @includeWhen(class_basename($data) === 'User', 'cms.partials.edit-user')
                    @includeWhen(class_basename($data) === 'Role', 'cms.partials.edit-role')
                </div>
            </div>
            <div class="card-footer d-flex gap-2 justify-content-end">
                <a href="{{ route($resource . '.index') }}" class="btn btn-light">
                    Back
                </a>
                <button type="submit" class="btn btn-primary" x-on:click="submitForm('editForm')">
                    Save
                </button>
        </form>
        @if (!class_basename($data) === 'User')
            @can('delete', $data)
                <form action="{{ route($resource . '.destroy', [$resource => $data]) }}" method="post" class="m-0 p-0">
                    @csrf
                    @method('delete')
                    <button type="submit" class="btn btn-danger">
                        Delete
                    </button>
                </form>
            @endcan
        @endif
    </div>
    </div>
@endsection
