@props(['family' => [], 'readonly' => false])
@php
    $genders = \App\Models\Sex::pluck('name', 'id');
    $relationships = \App\Models\Relationship::pluck('name', 'id');
    $civilStatus = \App\Models\CivilStatus::pluck('name', 'id');
@endphp
<div class="row">
    <div class="col-12 col-md-12 col-lg-8">
        <x-form-input name="fam_name" label="Name" value="{{ $family->name }}" required="true" :readonly="$readonly" />
    </div>
    <div class="col-12 col-md-6 col-lg-2">
        <x-form-select name="fam_sex_id" label="Sex" :options="$genders" selected="{{ $family->sex_id }}" required="true"
            :disabled="$readonly" />
    </div>
    <div class="col-12 col-md-6 col-lg-2">
        <x-form-input name="fam_age" label="Age" type="number" value="{{ $family->age }}" required="true"
            :readonly="$readonly" />
    </div>
</div>
<div class="row">
    <div class="col-6 col-md-6 col-lg-3">
        <x-form-select name="fam_civil_id" label="Civil Status" :options="$civilStatus" selected="{{ $family->civil_id }}"
            required="true" :disabled="$readonly" />
    </div>
    <div class="col-6 col-md-6 col-lg-3">
        <x-form-select name="fam_relationship_id" label="Relationship" :options="$relationships"
            selected="{{ $family->relationship_id }}" required="true" :disabled="$readonly" />
    </div>
    <div class="col-6 col-md-6 col-lg-3">
        <x-form-input name="fam_occupation" label="Occupation" value="{{ $family->occupation }}" :readonly="$readonly" />
    </div>
    <div class="col-6 col-md-6 col-lg-3">
        <x-form-input name="fam_income" label="Income" value="{{ $family->income }}" :readonly="$readonly" />
    </div>
</div>
<div class="d-flex justify-content-end">
    @if (!Route::is('beneficiary.family.show'))
        <a name="" id="" class="btn btn-warning btn-sm"
            href="{{ route('beneficiary.family.show', ['familyId' => $family->id]) }}" role="button">Edit
            Data</a>
    @endif
</div>
