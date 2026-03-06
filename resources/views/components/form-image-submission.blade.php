@props([
    'name' => null,
    'label' => null,
    'id' => null,
    'required' => false,
    'helpText' => false,
])

@php
    $errorname = $name;
    if (str_contains($name, 'images[')) {
        $errorname = str_replace('[', '.', str_replace(']', '', $name));
    }
@endphp

<div class="custom-file">
    @if ($label)
        <label for="{{ $name }}" class="form-label">{{ $label }}{{ $required ? '*' : '' }}</label>
    @endif
    <input type="file" class="form-control" name="{{ $name }}" id="{{ $id }}" placeholder=""
        {{ $helpText ? 'aria-describedby=fileHelpId' : '' }} accept="image/png, image/jpeg"
        {{ $required ? 'required' : '' }} />
    @if ($helpText)
        <div id="fileHelpId" class="form-text">{{ $helpText }}</div>
    @endif
    @error($errorname)
        <div class="alert alert-danger">{{ str_replace('images.', '', str_replace('_', ' ', $message)) }}</div>
    @enderror
</div>
