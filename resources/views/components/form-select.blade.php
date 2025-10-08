@props([
    'name',
    'id' => null,
    'label' => null,
    'options' => [],
    'selected' => null,
    'required' => false,
    'disabled' => false,
    'helpText' => false,
])

<div class="mb-3">
    @if ($label)
        <label for="{{ $name }}" class="form-label mb-0">{{ $label }}{{ $required ? '*' : '' }}</label>
    @endif

    <select
        class="form-control"
        name="{{ $name }}"
        id="{{ $id }}"
        {{ $disabled ? 'disabled' : '' }}
    >
        <option value="">Select one</option>
        @foreach ($options as $key => $value )
            <option value="{{ $key }}" {{ old($name, $selected) == $key ? 'selected' : '' }}>
                {{ $value }}
            </option>
        @endforeach
    </select>
</div>
