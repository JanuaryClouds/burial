@can('update-resource', $entry)
    <div id="edit-modal-{{ $entry->id }}" class="modal fade" tabindex="-1" role="dialog"
        aria-labelledby="edit-modal-{{ $entry->id }}" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            {{-- <form action="{{ route('cms.update', ['type' => $type, 'id' => $entry->id]) }}" method="post"> --}}
            <form action="{{ route('barangay.update', ['barangay' => $entry]) }} method="post">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="edit-modal-{{ $entry->id }}-title">Edit {{ $entry->name }} details
                        </h5>
                        <button class="btn btn-icon btn-sm btn-active-icon-primary" type="button" data-bs-dismiss="modal"
                            aria-label="Close">
                            <i class="ki-duotone ki-cross fs-1">
                                <span class="path1"></span><span class="path2"></span>
                            </i>
                        </button>
                    </div>
                    <div class="modal-body">
                        @foreach ($entry->getAttributes() as $field => $value)
                            @if (in_array($field, ['name', 'description', 'department', 'remarks', 'district_id']))
                                <x-form-input name="{{ $field }}" id="{{ $field }}"
                                    value="{{ $value }}" type="text"
                                    label="{{ Str::replace('_', ' ', ucfirst($field)) }}" />
                            @endif
                        @endforeach
                        @if (Request::is('roles'))
                            @foreach ($permissions as $permission)
                                @if (!in_array($permission->name, ['create', 'view', 'delete', 'edit']))
                                    @php
                                        $role = \Spatie\Permission\Models\Role::where('name', $entry->name)
                                            ->get()
                                            ->first();
                                    @endphp
                                    <div>
                                        <label>
                                            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                                                {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                            {{ Str::title(str_replace(['_', '-'], ' ', $permission->name)) }}
                                        </label>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                        @if (Request::is('cms/users'))
                            @php
                                $roles = \Spatie\Permission\Models\Role::where('id', '!=', 1)->get();
                            @endphp
                            <div class="form-group">
                                <label for="role">Role</label>
                                <select id="role" class="form-control" name="role">
                                    <option value="{{ $entry->getRoleNames()->first() }}">
                                        {{ $entry->getRoleNames()->first() }}</option>
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
                        <button class="btn btn-light" type="button" data-bs-dismiss="modal">
                            Cancel
                        </button>
                        <button class="btn btn-primary" type="submit">
                            Save
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endcan
