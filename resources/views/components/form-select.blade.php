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

@php
    $isInactive = $disabled ? ' bg-body text-gray-700' : '';
@endphp

<div class="mb-3">
    @if ($label)
        <label for="{{ $name }}"
            class="form-label">{{ $label }}{{ $required && !$disabled ? '*' : '' }}</label>
    @endif

    <select {{ $attributes->merge(['class' => 'form-control' . $isInactive]) }} name="{{ $name }}"
        id="{{ $id }}" {{ $disabled ? 'disabled' : '' }} {{ $required == true ? 'required' : '' }}>
        <option value="">Select one</option>
        @foreach ($options as $key => $value)
            <option value="{{ $key }}" {{ old($name, $selected) == $key ? 'selected' : '' }}>
                {{ $value }}
            </option>
        @endforeach
    </select>
</div>
