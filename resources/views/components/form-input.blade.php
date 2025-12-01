@props([
    'name',
    'id' => null,
    'label' => null,
    'type' => 'text',
    'placeholder' => null,
    'value' => '',
    'required' => false,
    'helpText' => false,
    'disabled' => false,
    'readonly' => false,
    'min' => null,
])

@php
    $isInactive = $disabled ? ' bg-body text-gray-700' : '';
@endphp

<div class="mb-3">
    @if ($label)
        <label for="{{ $name }}"
            class="form-label">{{ $label }}{{ $required && !$readonly ? '*' : '' }}</label>
    @endif
    <input type="{{ $type }}" {{ $attributes->merge(['class' => 'form-control' . $isInactive]) }}
        name="{{ $name }}" {{ $id ? 'id=' . $id : '' }} value="{{ old($name, $value) }}" aria-describedby="helpId"
        {{ $placeholder ? 'placeholder=' . $placeholder : '' }} {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }} {{ $readonly ? 'readonly' : '' }} {{ $min ? 'min=' . $min : '' }} />

    @if ($helpText)
        <small id="helpId" class="form-text text-muted">{{ $helpText }}</small>
    @endif
</div>
