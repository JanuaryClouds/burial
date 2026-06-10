<div class="d-flex flex-column gap-6">
    <div class="d-flex flex-column gap-4">
        @if (!$data->hasRole('superadmin') && auth()->user()->hasRole('superadmin'))
            <h5>Account Status</h5>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="{{ $data->is_active == '1' ? '1' : '0' }}"
                    name="is_active" id="activeCheck" {{ $data->is_active ? 'checked' : '' }} />
                <label class="form-check-label" for="activeCheck">
                    Active Account
                </label>
            </div>
        @elseif ($data->hasRole('superadmin'))
            <h5>Account Status</h5>
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
    @if ($data->emp_id != null && !$data->hasRole('staff'))
        <div class="alert alert-info mb-3" role="alert">
            <strong>Staff User:</strong> This user is a government employee. Check the 'staff' role if this user is
            considered as a staff of the system.
        </div>
    @endif
    @if (count($roles) > 0)
        <div class="d-flex flex-column gap-4">
            <h5>Roles</h5>
            @foreach ($roles as $role)
                @if ($data->hasRole('staff'))
                    @if (auth()->user()->can('edit-roles') && $role->name !== 'superadmin')
                        @if ($data->hasRole('superadmin') && $role->name === 'staff')
                            <div class="form-check">
                                <input type="hidden" name="roles[]" value="{{ $role->id }}">
                                <input class="form-check-input" type="checkbox" value="{{ $role->id }}"
                                    id="role{{ $role->id }}Check"
                                    {{ $data->roles->contains($role) ? 'checked' : '' }} disabled />
                                <label class="form-check-label" for="role{{ $role->id }}Check">
                                    {{ $role->name }}
                                </label>
                            </div>
                        @else
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="{{ $role->id }}"
                                    name="roles[]" id="role{{ $role->id }}Check"
                                    {{ $data->roles->contains($role) ? 'checked' : '' }} />
                                <label class="form-check-label" for="role{{ $role->id }}Check">
                                    {{ $role->name }}
                                </label>
                            </div>
                        @endif
                    @else
                        <div class="form-check">
                            @if ($data->roles->contains($role))
                                <input type="hidden" name="roles[]" value="{{ $role->id }}">
                            @endif
                            <input class="form-check-input" type="checkbox" value="{{ $role->id }}"
                                id="role{{ $role->id }}Check" {{ $data->roles->contains($role) ? 'checked' : '' }}
                                disabled />
                            <label class="form-check-label" for="role{{ $role->id }}Check">
                                {{ $role->name }}
                            </label>
                        </div>
                    @endif
                @else
                    @if ($role->name === 'staff' && $data->emp_id !== null)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="{{ $role->id }}" name="roles[]"
                                id="role{{ $role->id }}Check"
                                {{ $data->roles->contains($role) ? 'checked' : '' }} />
                            <label class="form-check-label" for="role{{ $role->id }}Check">
                                {{ $role->name }}
                            </label>
                        </div>
                    @endif
                @endif
            @endforeach
        </div>
    @endif
</div>
