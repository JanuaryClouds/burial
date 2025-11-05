<h5 class="card-title">I. CLIENT'S IDENTIFYING INFORMATION</h5>
    <div class="row">
        <div class="col-12 col-md-12 col-lg-8">
            <x-form-input 
                name="client[name]"
                label="1. Client's Name"
                required="true"
            />
        </div>
        <div class="col-6 col-md-6 col-lg-2">
            <x-form-input 
                name="client[age]"
                label="2. Age"
                required="true"
                type="number"
            />
        </div>
        <div class="col-6 col-md-6 col-lg-2">
            <x-form-select 
                name="client[gender]"
                id="gender"
                label="3. Sex"
                :options="$genders"
            />
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-md-3 col-lg-4">
            <x-form-input
                name="client[date_of_birth]"
                label="4. Date of Birth"
                required="true"
                type="date"
            />
        </div>
        <div class="col-12 col-md-9 col-lg-4">
            <x-form-input
                name="client[address]"
                label="5. Present Address"
                required="true"
                type="text"
            />
        </div>
        <div class="col-12 col-md-9 col-lg-4">
            <x-form-select
                name="client[barangay_id]"
                label="Barangay"
                required="true"
                :options="$barangays"
            />
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-md-6 col-lg-6">
            <x-form-select
                name="client[relationship_to_beneficiary]"
                label="6. Relationship to Beneficiary"
                required="true"
                :options="$relationships"
            />
        </div>
        <div class="col-12 col-md-6 col-lg-6">
            <x-form-select
                name="client[civil_status]"
                label="7. Civil Status"
                required="true"
                :options="$civilStatus"
            />
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-md-12 col-lg-4">
            <x-form-select 
                name="client[religion]"
                label="8. Religion"
                required="true"
                :options="$religions"
            />
        </div>
        <div class="col-12 col-md-12 col-lg-4">
            <x-form-select 
                name="client[nationality]"
                label="9. Nationality"
                required="true"
                :options="$nationalities"
            />
        </div>
        <div class="col-12 col-md-12 col-lg-4">
            <x-form-select 
                name="client[education]"
                label="10. Educational Attainment"
                required="true"
                :options="$educations"
            />
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-md-6 col-lg-6">
            <x-form-input 
                name="client[skills]"
                label="11. Skills/Occupation"
                required="false"
                type="text"
            />
        </div>
        <div class="col-12 col-md-6 col-lg-6">
            <x-form-input 
                name="client[income]"
                label="12. Estimated Monthly Income"
                required="false"
                type="text"
            />
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-md-8 col-lg-6">
            <x-form-input
                name="client[philhealth]"
                label="13. PhilHealth Number"
                required="false"
                type="text"
            />
        </div>
        <div class="col-12 col-md-8 col-lg-6">
            <x-form-input
                name="client[contact_number]"
                label="14. Contact Number"
                required="false"
                type="text"
            />
        </div>
    </div>