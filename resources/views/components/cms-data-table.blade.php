@props([
    'data',
    'type',
])
@php
    $excemptions = [
        'created_at',
        'updated_at',
        'extra_data_schema',
        'is_optional',
        'requires_extra_data',
        'email_verified_at',
        'password',
        'remember_token',
        'is_active',
    ];
@endphp
<div class="table-responsive">
    <div class="dataTables_wrapper container-fluid">
        <table id="cms-table" class="table data-table" style="width:100%">
            <thead>
                <tr role="row">
                    @foreach ($data->first()->getAttributes() as $column => $value)
                        @if (!in_array($column, $excemptions))
                            <th class="sorting sort-handler">{{ Str::replace('_', ' ', Str::title($column)) }}</th>
                        @endif
                    @endforeach
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $entry)
                    <tr class="bg-white">
                        @foreach ($entry->getAttributes() as $key =>$value)
                            @if(!in_array($key, $excemptions))
                                <td>{{ $value }}</td>
                            @endif
                        @endforeach
                        <td>
                            @if (Request::routeIs('superadmin.cms.users'))
                                @if (Request::routeIs('superadmin.cms.users') && !$entry?->hasRole('superadmin'))
                                    <form
                                        id="update-is-active-{{ $entry->id }}"
                                        action="{{ route('superadmin.cms.update', ['type' => $type, 'id' => $entry->id]) }}"
                                        method="post"
                                    >
                                        @csrf
                                        <div class="custom-control custom-switch">
                                            <input 
                                                id="is_active-{{ $entry->id }}" 
                                                class="custom-control-input" 
                                                type="checkbox" 
                                                name="is_active" 
                                                {{ $entry->is_active ? 'checked' : '' }}
                                                x-on:change="document.getElementById('update-is-active-{{ $entry->id }}').submit()"
                                            >
                                            <label for="is_active-{{ $entry->id }}" class="custom-control-label">Active</label>
                                        </div>
                                    </form>
                                @endif
                            @else
                                <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#edit-modal-{{ $entry->id }}">
                                    <i class="fas fa-edit"></i> 
                                </button>
                            @endif
                            @if (!Request::routeIs('superadmin.cms.workflow') && !Request::routeIs('superadmin.cms.handlers') && !Request::routeIs('superadmin.cms.users'))
                                <button class="btn btn-danger" type="button" data-toggle="modal" data-target="#delete-modal-{{ $entry->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                    
                    <!-- Edit content modal -->
                    <div id="edit-modal-{{ $entry->id }}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="edit-modal-{{ $entry->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <form action="{{ route('superadmin.cms.update', ['type' => $type, 'id' => $entry->id]) }}" method="post">
                                @csrf
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="edit-modal-{{ $entry->id }}-title">Edit {{ $entry->name }} details</h5>
                                        <button class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        @foreach ($entry->getAttributes() as $field => $value)
                                            @if(in_array($field, ['name', 'description', 'department', 'remarks', 'district_id']))
                                                <x-form-input
                                                    name="{{ $field }}"
                                                    id="{{ $field }}"
                                                    value="{{ $value }}"
                                                    type="text"
                                                    label="{{ Str::replace('_',' ',ucfirst($field)) }}"
                                                />
                                            @endif
                                        @endforeach  
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-primary" type="submit">
                                            <i class="fas fa-save"></i>
                                            Save
                                        </button>
                                        <button class="btn btn-secondary" type="button" data-dismiss="modal">
                                            <i class="fas fa-times"></i>
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Delete content modal -->
                    <div id="delete-modal-{{ $entry->id }}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="delete-modal-{{ $entry->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <form action="{{ route('superadmin.cms.delete', ['type' => $type, 'id' => $entry->id]) }}" method="post">
                                @csrf
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="delete-modal-{{ $entry->id }}-title">Delete {{ $entry->name }}</h5>
                                        <button class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <p>Are you sure you want to delete {{ $entry->name }} in the database? This will affect data connected to this.</p>
                                    </div>
                                    <div class="modal-footer">
                                        <button class="btn btn-danger" type="submit">
                                            <i class="fas fa-trash"></i>
                                            Confirm Deletion
                                        </button>
                                        <button class="btn btn-secondary" data-dismiss="modal" aria-label="Close">
                                            <i class="fas fa-times-circle"></i>
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @endforeach
            </tbody>
        </table>
    </div>
</div>