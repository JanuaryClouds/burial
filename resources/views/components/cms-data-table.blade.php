@props([
    'data',
    'type',
])
@php
    use Spatie\Permission\Models\Permission;
    use Spatie\Permission\Models\Role;
    $permissions = Permission::all();

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
                    @if (Request::is('users.manage'))
                        <th>Role</th>
                    @endif
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
                        @if (Request::is('users.manage'))
                            <td>{{ Str::title($entry->getRoleNames()[0]) }}</td>
                        @endif
                        <td>
                            @if (Request::routeIs('users.manage'))
                                <!-- ! UNUSED -->
                                @if (Request::routeIs('users.manage'))
                                    <a href="{{ route('user.manage', ['userId' => $entry->id]) }}">
                                        <button class="btn btn-primary" type="button">
                                            <i class="fas fa-edit"></i> 
                                        </button>
                                    </a>
                                @endif
                            @else
                                @if (class_basename($entry) == 'User')
                                    @if (!$entry?->hasRole('superadmin'))
                                        <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#edit-modal-{{ $entry->id }}">
                                            <i class="fas fa-edit"></i> 
                                        </button>
                                    @endif
                                @else
                                    <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#edit-modal-{{ $entry->id }}">
                                        <i class="fas fa-edit"></i> 
                                    </button>
                                @endif
                            @endif
                            @if (!Request::routeIs('cms.workflow') && !Request::routeIs('cms.handlers') && !Request::routeIs('cms.users') && !Request::routeIs('users.manage') && !Request::routeIs('permissions') && !Request::routeIs('roles'))
                                <button class="btn btn-danger" type="button" data-toggle="modal" data-target="#delete-modal-{{ $entry->id }}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                    
                    <!-- Edit content modal -->
                    <div id="edit-modal-{{ $entry->id }}" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="edit-modal-{{ $entry->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered" role="document">
                            <form action="{{ route('cms.update', ['type' => $type, 'id' => $entry->id]) }}" method="post">
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
                                        @if (Request::is('roles'))
                                            @foreach($permissions as $permission)
                                                @if (!in_array($permission->name, ['create', 'view', 'delete', 'edit']))
                                                    @php
                                                        $role = Role::where('name', $entry->name)->get()->first();
                                                    @endphp
                                                    <div>
                                                        <label>
                                                            <input 
                                                                type="checkbox" 
                                                                name="permissions[]" 
                                                                value="{{ $permission->name }}"
                                                                {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}
                                                            >
                                                            {{ Str::title(str_replace(['_', '-'],' ', $permission->name)) }}
                                                        </label>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                        @if (Request::is('cms/users'))
                                            @php
                                                $roles = Role::where('name', '!=', 'superadmin')->get();
                                            @endphp
                                            <div class="form-group">
                                                <label for="role">Role</label>
                                                <select id="role" class="form-control" name="role">
                                                    <option value="{{ $entry->getRoleNames()->first() }}">{{ $entry->getRoleNames()->first() }}</option>
                                                    @foreach ($roles as $role)
                                                        @if ($entry->getRoleNames()->first() != $role->name)
                                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                            </div>
                                        @endif
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
                            <form action="{{ route('cms.delete', ['type' => $type, 'id' => $entry->id]) }}" method="post">
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