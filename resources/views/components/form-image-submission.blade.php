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
    @php
        $helpId = $id ? $id . '-help' : 'file-HelpId' . uniqid();
    @endphp
    <input type="file" class="form-control" name="{{ $name ?? $id }}" {{ $id ? 'id="' . $id . '"' : '' }} placeholder=""
        {{ $helpText ? 'aria-describedby="' . $helpId . '"' : '' }} accept="image/jpeg,image/png"
        {{ $required ? 'required' : '' }} />
    @if ($helpText)
        <div id="{{ $helpId }}" class="form-text">{{ $helpText }}</div>
    @endif
    @error($errorname)
        <div class="alert alert-danger">{{ str_replace('images.', '', str_replace('_', ' ', $message)) }}</div>
    @enderror
</div>
