@extends('layouts.metronic.admin')
<title>Roles</title>
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h4>Roles</h4>
            </div>
        </section>
        <div class="section-body">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h4 class="card-title">Roles</h4>
                </div>
                <div class="card-body">
                    <x-cms-data-table type="roles" :data="$data" />
                </div>
            </div>
        </div>
        <div id="new-role-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="new-role-modal"
            aria-hidden="true">
            <form action="{{ route('role.store') }}" method="post">
                @csrf
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="newContent">Add New {{ Str::substr(Str::ucfirst($type), 0, -1) }}
                            </h5>
                            <button class="btn btn-icon btn-sm btn-active-icon-primary" type="button"
                                data-bs-dismiss="modal" aria-label="Close">
                                <i class="ki-duotone ki-cross fs-1">
                                    <span class="path1"></span><span class="path2"></span>
                                </i>
                            </button>
                        </div>
                        <div class="modal-body">
                            @foreach ($data->last()->getAttributes() as $field => $value)
                                @if (
                                    !in_array($field, [
                                        'id',
                                        'created_at',
                                        'updated_at',
                                        'email_verified_at',
                                        'guard_name',
                                        'remember_token',
                                        'is_active',
                                    ]))
                                    <div class="form-group">
                                        <label
                                            for="{{ $field }}">{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                                        <input type="text" name="{{ $field }}" id="{{ $field }}"
                                            class="form-control">
                                    </div>
                                @endif
                            @endforeach
                            @php
                                use App\Models\Permission;
                                $permissions = Permission::all();
                            @endphp
                            @if (Request::is('roles'))
                                @foreach ($permissions as $permission)
                                    <div>
                                        <label>
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                                {{ in_array($permission->name, ['create', 'view', 'delete', 'edit']) ? 'checked disabled' : '' }}>
                                            {{ Str::title(str_replace(['_', '-'], ' ', $permission->name)) }}
                                        </label>
                                    </div>
                                @endforeach
                            @endif
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
    </div>
@endsection
