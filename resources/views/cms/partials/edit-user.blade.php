<div class="d-flex flex-column gap-6">
    <div class="d-flex flex-column gap-4">
        @include('components.form-input', [
            'disabled' => true,
            'value' => $data->email,
            'label' => 'Email Address',
            'name' => 'email',
        ])
        @if (auth()->user()->can('update', $data) && $data->hasRole('staff'))
            @include('components.form-input', [
                'name' => 'current_password',
                'type' => 'password',
                'label' => 'Current Password',
                'value' => '',
                'autocomplete' => false,
            ])
            @include('components.form-input', [
                'name' => 'password',
                'type' => 'password',
                'label' => 'New Password',
                'value' => '',
                'autocomplete' => false,
            ])
            @include('components.form-input', [
                'name' => 'password_confirmation',
                'type' => 'password',
                'label' => 'Confirm Password',
                'value' => '',
                'autocomplete' => false,
            ])
        @endif
        @if (!$data->hasRole('superadmin') && auth()->user()->hasRole('superadmin'))
            <h5>Account Status</h5>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="{{ $data->is_active ? '1' : '0' }}" name="is_active"
                    id="activeCheck" {{ $data->is_active ? 'checked' : '' }} />
                <label class="form-check-label" for="activeCheck">
                    Active Account
                </label>
                <script nonce="{{ $nonce ?? '' }}">
                    $('#activeCheck').on('click', function() {
                        $(this).val($(this).is(':checked') ? '1' : '0');
                    });
                </script>
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
    @if (count($roles) > 0)
        <div class="d-flex flex-column gap-4">
            @if ($data->roles->count() > 0)
                <h5>Roles</h5>
            @endif
            @foreach ($roles as $role)
                @if ($data->hasRole('staff'))
                    @if (auth()->user()->can('edit-roles') && $role->name !== 'superadmin')
                        @if ($data->hasRole('superadmin') || $role->name === 'staff')
                            <div class="form-check">
                                <input type="hidden" name="roles[]" value="{{ $role->id }}">
                                <input class="form-check-input" type="checkbox" value="{{ $role->id }}"
                                    id="role{{ $role->id }}Check" checked disabled />
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
                @endif
            @endforeach
        </div>
    @endif
</div>
