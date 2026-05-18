<div class="d-flex flex-column gap-6">
    <div class="d-flex flex-column gap-4">
        @can('update', $data)
            <h5>Update Password</h5>
            <x-form-input name="password" label="Password" type="password" />
            <x-form-input name="password_confirmation" label="Confirm Password" type="password" />
        @endcan
        <h5>Account Status</h5>
        @if (auth()->user()->can('edit-users') && !auth()->user()->hasRole('superadmin'))
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
    @if ($data->roles()->exists())
        @if (count($roles) > 0)
            <div class="d-flex flex-column gap-4">
                <h5>Roles</h5>
                @foreach ($roles as $role)
                    <div class="form-check">
                        @if ($role->name == 'staff')
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
                            @can('edit-roles')
                                <input class="form-check-input" type="checkbox" value="{{ $role->id }}" name="roles[]"
                                    id="role{{ $role->id }}Check"
                                    {{ $data->roles->contains($role) ? 'checked' : '' }} />
                                <label class="form-check-label" for="role{{ $role->id }}Check">
                                    {{ $role->name }}
                                </label>
                            @else
                                @if ($data->roles->contains($role))
                                    <input type="hidden" names="roles[]" value="{{ $role->id }}">
                                @endif
                                <input class="form-check-input" type="checkbox" value="{{ $role->id }}"
                                    id="role{{ $role->id }}Check" {{ $data->roles->contains($role) ? 'checked' : '' }}
                                    disabled />
                                <label class="form-check-label" for="role{{ $role->id }}Check">
                                    {{ $role->name }}
                                </label>
                            @endcan
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    @endif
</div>
