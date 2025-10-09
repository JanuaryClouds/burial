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
    'min' => null
])

<div class="mb-3">
    @if ($label)
        <label for="{{ $name }}">{{ $label }}{{ $required ? '*' : '' }}</label>
    @endif
    <input
        type="{{ $type }}"
        class="form-control"
        name="{{ $name }}"
        id="{{ $id }}"
        value="{{ old($name, $value) }}"
        aria-describedby="helpId"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $readonly ? 'readonly' : '' }}
        min="{{ $min }}"
    />

    @if ($helpText)
        <small id="helpId" class="form-text text-muted">{{ $helpText }}</small>        
    @endif
</div>
