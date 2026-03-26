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
    'autocomplete' => false,
])

@php
    $isInactive = $disabled ? ' bg-body text-gray-700' : '';
    $errorname = $name;
    if (str_contains($name, '[')) {
        $errorname = str_replace('[', '.', str_replace(']', '', $name));
    }
@endphp

<div class="mb-3">
    @if ($label)
        <label for="{{ $id ?? $name }}"
            class="form-label">{{ $label }}{{ $required && !$readonly ? '*' : '' }}</label>
    @endif
    <input type="{{ $type }}" {{ $attributes->merge(['class' => 'form-control' . $isInactive]) }}
        name="{{ $name ?? $id }}" {{ $id ? 'id=' . $id : 'id=' . $name }} value="{{ old($name, $value) }}"
        aria-describedby="helpId" {{ $placeholder ? 'placeholder=' . $placeholder : '' }}
        {{ $required ? 'required' : '' }} {{ $disabled ? 'disabled' : '' }} {{ $readonly ? 'readonly' : '' }}
        {{ $min ? 'min=' . $min : '' }} autocomplete="{{ $autocomplete ? 'on' : 'off' }}" />

    @if ($helpText)
        <small id="helpId" class="form-text text-muted">{{ $helpText }}</small>
    @endif
    @error($errorname)
        <small class="form-text text-danger">{{ $message }}</small>
    @enderror
</div>
