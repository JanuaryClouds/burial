<div class="d-flex flex-column gap-6">
    <div class="d-flex flex-column gap-4">
        <h5>Account Status</h5>
        <div class="form-check">
            <input class="form-check-input" type="checkbox" value="{{ $data->is_active == '1' ? '1' : '0' }}"
                name="is_active" id="activeCheck" {{ $data->is_active ? 'checked' : '' }} />
            <label class="form-check-label" for="activeCheck">
                Active Account
            </label>
        </div>
    </div>
    @if ($data->roles()->exists())
        @if (count($roles) > 0)
            <div class="d-flex flex-column gap-4">
                <h5>Roles</h5>
                @foreach ($roles as $role)
                    <div class="form-check">
                        @if ($role->name == 'staff')
                            <input type="hidden" name="roles[]" value="{{ $role->id }}"
                                value="{{ $data->roles->contains($role) ? '1' : '' }}" />
                            <input class="form-check-input" type="checkbox" value="{{ $role->id }}"
                                id="role{{ $role->id }}Check" {{ $data->roles->contains($role) ? 'checked' : '' }}
                                disabled />
                            <label class="form-check-label" for="role{{ $role->id }}Check">
                                {{ $role->name }}
                            </label>
                        @else
                            <input class="form-check-input" type="checkbox" value="{{ $role->id }}" name="roles[]"
                                id="role{{ $role->id }}Check"
                                {{ $data->roles->contains($role) ? 'checked' : '' }} />
                            <label class="form-check-label" for="role{{ $role->id }}Check">
                                {{ $role->name }}
                            </label>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    @endif
</div>
