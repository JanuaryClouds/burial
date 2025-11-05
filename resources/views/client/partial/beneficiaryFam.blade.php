<h5 class="card-title">III. BENEFICIARY'S FAMILY COMPOSITION</h5>
<div 
    x-data="{
        families: [ {} ],  // Start with 1 row
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
    }"
>

    <template x-for="(family, index) in families" :key="index">
        <div>
            <div class="row">
                <div class="col-12 col-md-12 col-lg-8">
                    <x-form-input 
                        name="beneficiary_family[name][]"
                        label="Name"
                    />
                </div>
                <div class="col-12 col-md-6 col-lg-2">
                    <x-form-select 
                        name="beneficiary_family[gender][]"
                        label="Sex"
                        :options="$genders"
                    />
                </div>
                <div class="col-12 col-md-6 col-lg-2">
                    <x-form-input 
                        name="beneficiary_family[age][]"
                        label="Age"
                        type="number"
                    />
                </div>
            </div>
            <div class="row">
                <div class="col-6 col-md-6 col-lg-3">
                    <x-form-select 
                        name="beneficiary_family[civil_status][]"
                        label="Civil Status"
                        :options="$civilStatus"
                    />
                </div>
                <div class="col-6 col-md-6 col-lg-3">
                    <x-form-select 
                        name="beneficiary_family[relationship_to_beneficiary][]"
                        label="Relationship"
                        :options="$relationships"
                    />
                </div>
                <div class="col-6 col-md-6 col-lg-3">
                    <x-form-input 
                        name="beneficiary_family[occupation][]"
                        label="Occupation"
                    />
                </div>
                <div class="col-6 col-md-6 col-lg-3">
                    <x-form-input 
                        name="beneficiary_family[income][]"
                        label="Income"
                    />
                </div>
            </div>
            <div class="d-flex justify-content-end align-items-center">
                <button
                    type="button"
                    class="btn btn-danger"
                    x-on:click="removeFamily(index)"
                    x-show="families.length > 1 && index > 0"
                >
                    <i class="fa fa-trash"></i> Remove
                </button>
            </div>
            <hr>
        </div>
    </template>

    <div class="mt-3" x-show="families.length < maxRows">
        <button 
            type="button"
            class="btn btn-sm btn-primary"
            @click="addFamily"
        >
            <i class="fa fa-plus"></i> Add Family Member
        </button>
    </div>
</div>

<script>
    function showLimitModal() {
        swal('Notice', "You have reached the maximum number of family members.", "info" )
    }
</script>