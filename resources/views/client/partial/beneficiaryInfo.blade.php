<h5 class="card-title">II. BENEFICIARY'S IDENTIFYING INFORMATION</h5>
    <div class="row">
        <div class="col-12 col-md-8 col-lg-8">
            <x-form-input 
                name="beneficiary[name]"
                label="1. Beneficiary's Name"
                required="true"
                type="text"
            />
        </div>
        <div class="col-12 col-md-4 col-lg-4">
            <x-form-select
                name="beneficiary[gender]"
                label="2. Sex"
                required="true"
                :options="$genders"
            />
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-md-6 col-lg-4">
            <x-form-input
                name="beneficiary[date_of_birth]"
                label="3. Date of Birth"
                required="true"
                type="date"
            />
        </div>
        <div class="col-12 col-md-6 col-lg-4">
            <x-form-input
                name="beneficiary[place_of_birth]"
                label="4. Place of Birth"
                required="true"
                type="text"
            />
        </div>
        <div class="col-12 col-md-12 col-lg-4">
            <x-form-select
                name="beneficiary[barangay_id]"
                label="Barangay"
                required="true"
                :options="$barangays"
            />
        </div>
    </div>