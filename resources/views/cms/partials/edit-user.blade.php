<div class="d-flex flex-column gap-5">
    <div class="form-group">
        <label for="role">Role</label>
        <select id="role" class="form-control" name="role">
            <option value="{{ $data->getRoleNames()->first() }}">{{ $data->getRoleNames()->first() }}</option>
            @foreach ($roles as $role)
                @if ($data->getRoleNames()->first() != $role->name)
                    <option value="{{ $role->name }}">{{ $role->name }}</option>
                @endif
            @endforeach
        </select>
    </div>
    <div class="form-check">
        <input class="form-check-input" type="checkbox" value="{{ $data->is_active == '1' ? '1' : '0' }}"
            name="is_active" id="flexCheckChecked" {{ $data->is_active ? 'checked' : '' }} />
        <label class="form-check-label" for="flexCheckChecked">
            Active
        </label>
    </div>
</div>
