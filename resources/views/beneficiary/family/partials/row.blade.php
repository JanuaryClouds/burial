@php
    $rowId = $id ?? 'INDEX';
    $rowReadonly = $readonly ?? false;
    $rowShowRemove = $showRemove ?? true;
    $rowMember = $member ?? [];
@endphp

<div class="family-member-row">
    <div class="row">
        <div class="col-12 col-md-12 col-lg-8">
            <x-form.input name="fam_name[]"
                :id="'fam_name_' . $rowId"
                label="Name"
                :value="$rowMember['name'] ?? ''"
                :required="true"
                :readonly="$rowReadonly"
                :errorname="'fam_name.' . $rowId" />
        </div>
        <div class="col-12 col-md-6 col-lg-2">
            <x-form.select name="fam_sex_id[]"
                :id="'fam_sex_id_' . $rowId"
                label="Sex"
                :options="$genders ?? []"
                :selected="$rowMember['sex_id'] ?? ''"
                :required="true"
                :disabled="$rowReadonly"
                :errorname="'fam_sex_id.' . $rowId" />
        </div>
        <div class="col-12 col-md-6 col-lg-2">
            <x-form.input name="fam_age[]"
                :id="'fam_age_' . $rowId"
                label="Age"
                type="number"
                min="0"
                :value="$rowMember['age'] ?? ''"
                :required="true"
                :readonly="$rowReadonly"
                :errorname="'fam_age.' . $rowId" />
        </div>
    </div>
    <div class="row">
        <div class="col-6 col-md-6 col-lg-3">
            <x-form.select name="fam_civil_id[]"
                :id="'fam_civil_id_' . $rowId"
                label="Civil Status"
                :options="$civilStatus ?? []"
                :selected="$rowMember['civil_id'] ?? ''"
                :required="true"
                :disabled="$rowReadonly"
                :errorname="'fam_civil_id.' . $rowId" />
        </div>
        <div class="col-6 col-md-6 col-lg-3">
            <x-form.select name="fam_relationship_id[]"
                :id="'fam_relationship_id_' . $rowId"
                label="Relationship"
                :options="$relationships ?? []"
                :selected="$rowMember['relationship_id'] ?? ''"
                :required="true"
                :disabled="$rowReadonly"
                :errorname="'fam_relationship_id.' . $rowId" />
        </div>
        <div class="col-6 col-md-6 col-lg-3">
            <x-form.input name="fam_occupation[]"
                :id="'fam_occupation_' . $rowId"
                label="Occupation"
                :value="$rowMember['occupation'] ?? ''"
                :readonly="$rowReadonly"
                :errorname="'fam_occupation.' . $rowId" />
        </div>
        <div class="col-6 col-md-6 col-lg-3">
            <x-form.input name="fam_income[]"
                :id="'fam_income_' . $rowId"
                label="Income"
                :value="$rowMember['income'] ?? ''"
                :readonly="$rowReadonly"
                :errorname="'fam_income.' . $rowId" />
        </div>
    </div>

    @if (!$rowReadonly && $rowShowRemove)
        <div class="d-flex justify-content-end align-items-center">
            <button type="button"
                class="btn btn-sm btn-danger family-remove-btn"
                data-family-remove>
                <i class="fa fa-trash"></i> Remove
            </button>
        </div>
    @endif
    <hr>
</div>
