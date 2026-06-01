<div class="d-flex flex-column gap-6">
    <div class="d-flex flex-column gap-4">
        @can('update', $data)
            <h5>Update Password</h5>
            <x-form-input name="password" label="Password" type="password" :autocomplete="false" />
            <x-form-input name="password_confirmation" label="Confirm Password" type="password" :autocomplete="false" />
        @endcan
        <h5>Account Status</h5>
        @if (auth()->user()->can('edit-users') && !$data->hasRole('superadmin'))
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="{{ $data->is_active == '1' ? '1' : '0' }}"
                    name="is_active" id="activeCheck" {{ $data->is_active ? 'checked' : '' }} />
                <label class="form-check-label" for="activeCheck">
                    Active Account
                </label>
            </div>
        @else
            <input type="hidden" name="is_active" value="{{ $data->is_active == '1' ? '1' : '0' }}" />
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="{{ $data->is_active == '1' ? '1' : '0' }}"
                    name="is_active_display" id="activeCheck" {{ $data->is_active ? 'checked' : '' }} disabled />
                <label class="form-check-label" for="activeCheck">
                    Active Account
                </label>
            </div>
        @endif
    </div>
    @if ($data->emp_id != null)
        @if (!$data->hasRole('staff'))
            <div class="alert alert-info mb-3" role="alert">
                <strong>Staff User:</strong> This user is a government employee. Check the 'staff' role if this user is
                considered as a staff of the system.
            </div>
        @endif
    @endif
    @if (count($roles) > 0)
        <div class="d-flex flex-column gap-4">
            <h5>Roles</h5>
            @foreach ($roles as $role)
                <div class="form-check">
                    @if ($role->name == 'superadmin')
                        @if ($data->roles->contains($role))
                            <input type="hidden" name="roles[]" value="{{ $role->id }}" />
                        @endif
                        <input class="form-check-input" type="checkbox" value="{{ $role->id }}"
                            id="role{{ $role->id }}Check" {{ $data->roles->contains($role) ? 'checked' : '' }}
                            disabled />
                        <label class="form-check-label" for="role{{ $role->id }}Check">
                            {{ $role->name }}
                        </label>
                    @else
                        @if ($data->hasRole('staff'))
                            @can('edit-roles')
                                <input class="form-check-input" type="checkbox" value="{{ $role->id }}" name="roles[]"
                                    id="role{{ $role->id }}Check"
                                    {{ $data->roles->contains($role) ? 'checked' : '' }} />
                                <label class="form-check-label" for="role{{ $role->id }}Check">
                                    {{ $role->name }}
                                </label>
                            @else
                                @if ($data->roles->contains($role))
                                    <input type="hidden" name="roles[]" value="{{ $role->id }}">
                                @endif
                                <input class="form-check-input" type="checkbox" value="{{ $role->id }}"
                                    id="role{{ $role->id }}Check" {{ $data->roles->contains($role) ? 'checked' : '' }}
                                    disabled />
                                <label class="form-check-label" for="role{{ $role->id }}Check">
                                    {{ $role->name }}
                                </label>
                            @endcan
                        @elseif ($role->name == 'staff')
                            <input class="form-check-input" type="checkbox" value="{{ $role->id }}" name="roles[]"
                                id="role{{ $role->id }}Check"
                                {{ $data->roles->contains($role) ? 'checked' : '' }} />
                            <label class="form-check-label" for="role{{ $role->id }}Check">
                                {{ $role->name }}
                            </label>
                        @endif
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>
