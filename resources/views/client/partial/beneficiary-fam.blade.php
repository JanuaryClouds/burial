@props([
    'readonly' => false,
])
@php
    $genders = \App\Models\Sex::pluck('name', 'id');
    $relationships = \App\Models\Relationship::pluck('name', 'id');
    $civilStatus = \App\Models\CivilStatus::pluck('name', 'id');

    if (isset($client)) {
        $familiesData = $client->family
            ->map(function ($family) {
                return [
                    'name' => $family->name,
                    'sex_id' => $family->sex_id,
                    'civil_id' => $family->civil_id,
                    'age' => $family->age,
                    'relationship_id' => $family->relationship_id,
                    'occupation' => $family->occupation,
                    'income' => $family->income,
                ];
            })
            ->values();
    } else {
        $familiesData = [];
    }
@endphp
@if (Request::routeIs('general.intake.form') || $familiesData->count() > 0)
    <h5 class="card-title">III. BENEFICIARY'S FAMILY COMPOSITION</h5>
    <div x-data="{
        families: {{ $familiesData ? $familiesData->toJson() : '[{}]' }}, // Start with 1 row
        maxRows: 5,
        addFamily() {
            if (this.families.length < this.maxRows) {
                this.families.push({});
            } else {
                showLimitModal();
            }
        },
        removeFamily(index) {
            this.families.splice(index, 1);
        }
    }">
        <template x-for="(family, index) in families" :key="index">
            <div>
                <div class="row">
                    <div class="col-12 col-md-12 col-lg-8">
                        <x-form-input name="fam_name[]" label="Name" x-model="family.name" x-bind:required="index === 0"
                            :readonly="$readonly" />
                    </div>
                    <div class="col-12 col-md-6 col-lg-2">
                        <x-form-select name="fam_sex_id[]" label="Sex" :options="$genders" x-model="family.sex_id"
                            x-bind:required="index === 0" :disabled="$readonly" />
                    </div>
                    <div class="col-12 col-md-6 col-lg-2">
                        <x-form-input name="fam_age[]" label="Age" type="number" x-model="family.age"
                            x-bind:required="index === 0" :readonly="$readonly" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-6 col-md-6 col-lg-3">
                        <x-form-select name="fam_civil_id[]" label="Civil Status" :options="$civilStatus"
                            x-model="family.civil_id" x-bind:required="index === 0" :disabled="$readonly" />
                    </div>
                    <div class="col-6 col-md-6 col-lg-3">
                        <x-form-select name="fam_relationship_id[]" label="Relationship" :options="$relationships"
                            x-model="family.relationship_id" x-bind:required="index === 0" :disabled="$readonly" />
                    </div>
                    <div class="col-6 col-md-6 col-lg-3">
                        <x-form-input name="fam_occupation[]" label="Occupation" x-model="family.occupation"
                            :readonly="$readonly" />
                    </div>
                    <div class="col-6 col-md-6 col-lg-3">
                        <x-form-input name="fam_income[]" label="Income" x-model="family.income" :readonly="$readonly" />
                    </div>
                </div>

                @if (Request::routeIs('general.intake.form'))
                    <div class="d-flex justify-content-end align-items-center">
                        <button type="button" class="btn btn-danger" x-on:click="removeFamily(index)"
                            x-show="families.length > 1 && index > 0">
                            <i class="fa fa-trash"></i> Remove
                        </button>
                    </div>
                @endif
                <hr>
            </div>
        </template>

        @if (Route::is('client.create'))
            <div class="mt-3" x-show="families.length < maxRows">
                <button type="button" class="btn btn-sm btn-primary" @click="addFamily">
                    <i class="fa fa-plus"></i> Add Family Member
                </button>
            </div>
        @endif
    </div>

    <script>
        function showLimitModal() {
            swal('Notice', "You have reached the maximum number of family members.", "info")
        }
    </script>
@endif
