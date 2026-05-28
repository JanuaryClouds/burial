@extends('layouts.app')
@section('content')
    <div class="d-flex flex-column gap-4">
        <div class="card">
            <form action="{{ route($resource . '.update', [$resource => $data]) }}" method="post" id="editForm">
                @csrf
                @method('PUT')
                <div class="card-header">
                    <h4 class="card-title">{{ isset($data->name) ? $data?->name : ucfirst($resource) }}</h4>
                </div>
                <div class="card-body">
                    <div class="d-flex flex-column gap-3">
                        @foreach ($data->getAttributes() as $field => $value)
                            <div class="row">
                                {{-- List of skipped fields --}}
                                @if (
                                    !in_array($field, [
                                        'id',
                                        'citizen_uuid',
                                        'created_at',
                                        'updated_at',
                                        'deleted_at',
                                        'handler_id',
                                        'district_id',
                                        'is_optional',
                                        'requires_extra_data',
                                        'extra_data_schema',
                                        'order_no',
                                        'email',
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
                                            'autocomplete' => false,
                                        ])
                                    </div>
                                @endif
                            </div>
                        @endforeach
                        @includeWhen($data instanceof \App\Models\User, 'cms.partials.edit-user')
                        @includeWhen($data instanceof \Spatie\Permission\Models\Role, 'cms.partials.edit-role')
                    </div>
                </div>
                <div class="card-footer d-flex gap-2 justify-content-end">
                    @if (Route::has($resource . '.update') && auth()->user()->hasRole('superadmin'))
                        <button type="submit" class="btn btn-primary btn-sm">
                            Save
                        </button>
                    @endif
                </div>
            </form>
        </div>
        <div class="d-flex justify-content-center align-items-center gap-2">
            @if (Route::has($resource . '.destroy') && auth()->user()->hasRole('superadmin') && is_null($data->deleted_at))
                <form action="{{ route($resource . '.destroy', $data->id) }}" method="post">
                    @csrf
                    @method('delete')
                    <button type="submit" class="btn btn-danger btn-sm">
                        Disable
                    </button>
                </form>
            @endif
            @if (Route::has($resource . '.restore') && auth()->user()->hasRole('superadmin') && !is_null($data->deleted_at))
                <form action="{{ route($resource . '.restore', $data->id) }}" method="post">
                    @csrf
                    <button type="submit" class="btn btn-success btn-sm">
                        Restore
                    </button>
                </form>
            @endif
        </div>
    </div>
@endsection
