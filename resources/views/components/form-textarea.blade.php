@props([
    'name',
    'id' => null,
    'label' => null,
    'placeholder' => 'Aa',
    'value' => '',
    'required' => false,
    'readonly' => false,
    'disabled' => false,
])

@php
    $isInactive = $disabled ? ' bg-body text-gray-700' : '';
@endphp

<div class="mb-3">
    <label for="{{ $name }}" class="form-label">{{ $label }}</label>
    <textarea {{ $attributes->merge(['class' => 'form-control' . $isInactive]) }} name="{{ $name }}"
        id="{{ $id }}" value="" rows="3" {{ $required ? 'required' : '' }}
        {{ $readonly ? 'readonly' : '' }} {{ $disabled ? 'disabled' : '' }}>{{ old($name, $value) }}</textarea>
</div>
