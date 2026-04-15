@props([
    'name',
    'id' => null,
    'label' => null,
    'options' => [],
    'selected' => null,
    'required' => false,
    'disabled' => false,
    'helpText' => false,
    'errorname' => null,
])

@php
    $isInactive = $disabled ? ' bg-body text-gray-700' : '';
    if ($errorname == null) {
        if (str_contains($name, '[')) {
            $errorname = str_replace('[', '.', str_replace(']', '', $name));
        } else {
            $errorname = $name;
        }
    }
@endphp

<div class="mb-3">
    @if ($label)
        <label for="{{ $name ?? $id }}"
            class="form-label">{{ $label }}{{ $required && !$disabled ? '*' : '' }}</label>
    @endif

    <select {{ $attributes->merge(['class' => 'form-control' . $isInactive]) }} name="{{ $name }}"
        id="{{ $id ?? $name }}" {{ $disabled ? 'disabled' : '' }} {{ $required == true ? 'required' : '' }}>
        <option value="">Select one</option>
        @foreach ($options as $key => $value)
            <option value="{{ $key }}" {{ old($name, $selected) == $key ? 'selected' : '' }}>
                {{ $value }}
            </option>
        @endforeach
    </select>
    @error($errorname)
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
