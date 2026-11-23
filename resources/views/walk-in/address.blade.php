<h4 class="mt-6">{{ $title }}</h4>
@php
    $addressTarget = 'data.'.$group.'.regionCode,data.'.$group.'.provinceCode,data.'.$group.'.municipalityCode,data.'.$group.'.barangayCode';
@endphp
<fieldset class="row g-3" wire:loading.attr="disabled" wire:target="{{ $addressTarget }}">
    @foreach (['regionCode' => 'Region', 'provinceCode' => 'Province/City', 'municipalityCode' => 'Municipality', 'barangayCode' => 'Barangay'] as $field => $label)
        <div class="col-sm-6 col-lg-3" wire:key="{{ $group }}-{{ $field }}"><x-form.select :name="'data.'.$group.'.'.$field" :label="$label" :options="$addresses[$group][$field]" :selected="data_get($data, $group.'.'.$field)" wire:model.live="data.{{ $group }}.{{ $field }}" /></div>
    @endforeach
    <div class="col-sm-8"><x-form.input :name="'data.'.$group.'.street'" label="Street" wire:model="data.{{ $group }}.street" maxlength="255" /></div>
    <div class="col-sm-4"><x-form.input :name="'data.'.$group.'.houseNumber'" label="House Number" wire:model="data.{{ $group }}.houseNumber" maxlength="255" /></div>
    <div class="col-12 align-items-center gap-2 text-primary" wire:loading.flex wire:target="{{ $addressTarget }}" role="status" aria-live="polite">
        <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
        Loading address options. Please wait…
    </div>
</fieldset>
