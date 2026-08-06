@props([
    'name',
    'id' => null,
    'label' => null,
    'options' => [],
    'multiselect' => false,
    'selected' => null,
    'required' => false,
    'readonly' => false,
    'helpText' => false,
    'errorname' => null,
])

@php
    $isInactive = $readonly ? ' bg-body text-gray-700' : '';
    if ($errorname == null) {
        if (str_contains($name, '[')) {
            $errorname = str_replace('[', '.', str_replace(']', '', $name));
        } else {
            $errorname = $name;
        }
    }
@endphp

<div class="mb-3" wire:ignore>
    @if ($label)
        @if (app()->hasDebugModeEnabled())
            <label for="{{ $id ?? $name }}" class="form-label" data-bs-toggle="tooltip" data-bs-placement="top"
                title="{{ 'Name: ' . $name . ', Error: ' . $errorname }}">
                {{ $label }}{{ $required ? ' *' : '' }}
            </label>
        @else
            <label for="{{ $id ?? $name }}" class="form-label">
                {{ $label }}{{ $required ? ' *' : '' }}
            </label>
        @endif
    @endif

    <select {{ $attributes->merge(['class' => 'form-control' . $isInactive]) }} name="{{ $name }}" {{ $multiselect ? 'multiple' : '' }}
        id="{{ $id ?? $name }}" {{ $readonly ? 'disabled' : '' }} {{ $required == true ? 'required' : '' }} data-control="select2">
        <option value="">Select one</option>
        @foreach ($options as $key => $value)
            <option value="{{ $key }}" {{ old($name, $selected) == $key ? 'selected' : '' }}>{{ $value }}</option>
        @endforeach
    </select>
    @error($errorname)
        <span class="text-danger">{{ $message }}</span>
    @enderror
</div>
