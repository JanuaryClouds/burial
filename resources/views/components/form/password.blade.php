@props(['name', 'id' => null, 'label', 'autocomplete' => 'new-password'])
<x-form.input :name="$name" :id="$id ?? $name" :label="$label" type="password" :autocomplete="$autocomplete" :required="true" {{ $attributes }}>
    <x-slot:append>
        <button type="button" class="btn btn-light border" data-password-visibility
            aria-controls="{{ $id ?? $name }}" aria-label="Show {{ $label }}" aria-pressed="false"
            data-password-label="{{ $label }}">Show</button>
    </x-slot:append>
</x-form.input>
