@props([
    'name',
    'label' => null,
    'options' => collect(),
    'optionValue' => 'id',
    'optionLabel' => 'name',
    'selected' => [],
    'required' => false,
])

<div class="mb-3" wire:ignore>
    @if ($label)
        <label class="form-label">
            {{ $label }}
        </label>
    @endif

    <select name="{{ $name }}[]" multiple {{ $attributes->merge(['class' => 'form-control']) }}
        data-control="select2">
        @foreach ($options as $option)
            <option value="{{ data_get($option, $optionValue) }}" @selected(in_array(data_get($option, $optionValue), $selected))>{{ data_get($option, $optionLabel) }}</option>
        @endforeach
    </select>

    @error($name)
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @enderror
</div>
