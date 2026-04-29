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
                            @if (
                                !in_array($field, [
                                    'id',
                                    'citizen_uuid',
                                    'created_at',
                                    'updated_at',
                                    'handler_id',
                                    'district_id',
                                    'is_optional',
                                    'requires_extra_data',
                                    'extra_data_schema',
                                    'order_no',
                                    'email_verified_at',
                                    'password',
                                    'remember_token',
                                    'is_active',
                                ]))
                                @php
                                    if ($data instanceof \App\Models\User) {
                                        $encryptedFields = [
                                            'last_name',
                                            'first_name',
                                            'middle_name',
                                            'suffix',
                                            'contact_number',
                                        ];
                                        if (in_array($field, $encryptedFields)) {
                                            try {
                                                $value = Crypt::decryptString($value);
                                            } catch (\Throwable $th) {
                                                \Log::warning('Failed to decrypt ' . $field);
                                            }
                                        }
                                    }
                                @endphp
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
                    @includeWhen($data instanceof \App\Models\User, 'cms.partials.edit-user')
                    @includeWhen($data instanceof \App\Models\Role, 'cms.partials.edit-role')
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
@endsection
