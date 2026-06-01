<div class="row">
    <div class="col d-flex flex-column gap-2">
        @foreach ($permissions as $permission)
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="permissions[]" value="{{ $permission->name }}"
                    id="permission{{ $permission->id }}Check"
                    {{ in_array($permission->name, $data->permissions->pluck('name')->toArray()) ? 'checked' : '' }}>
                <label class="form-check-label" for="permission{{ $permission->id }}Check">
                    {{ Str::title(str_replace(['_', '-'], ' ', $permission->name)) }}
                </label>
            </div>
        @endforeach
    </div>
</div>
