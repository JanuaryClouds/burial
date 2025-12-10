@foreach ($permissions as $permission)
    <div>
        <label>
            <input type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                {{ in_array($permission->name, ['create', 'view', 'delete', 'edit']) ? 'checked disabled' : '' }}
                {{ in_array($permission->name, $data->permissions->pluck('name')->toArray()) ? 'checked' : '' }}>
            {{ Str::title(str_replace(['_', '-'], ' ', $permission->name)) }}
        </label>
    </div>
@endforeach
