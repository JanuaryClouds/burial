@props([
    'name',
    'label' => null,
    'id' => null,
    'required' => false,
    'helpText' => false,  
])

<div class="custom-file">
    @if ($label)
        <label for="{{ $name }}" class="form-label">{{ $label }}{{ $required ? '*' : '' }}</label>
    @endif
    <input
        type="file"
        class="form-control"
        name="{{ $name }}"
        id="{{ $id }}"
        placeholder=""
        aria-describedby="fileHelpId"
        {{ $required ? 'required' : '' }}
    />
    @if ($helpText)
        <div id="fileHelpId" class="form-text">{{ $helpText }}</div>
    @endif
</div>
