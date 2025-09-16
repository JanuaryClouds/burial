<div class="bg-white shadow-sm rounded p-4">
    <h2>Image Requirements</h2>
    <div class="row flex-column justify-content-center align-items-center g-2">
        <div
            class="col"
        >
            <x-form-image-submission
                name="burial_assistance[death_certificate]"
                label="Certified True Copy of Registered Death Certificate"
                helpText="From Taguig City Civil Registry."
            />
        </div>
        <div
            class="col"
        >
            <x-form-image-submission
                name="burial_assistance[funeral_contract]"
                label="Certified True Copy of Funeral Contract"
                helpText="From Funeral Establishment."
            />
        </div>
        <div
            class="col"
        >
            <div class="row">
                <div class="col">
                    <x-form-image-submission
                        name="burial_assistance[claimant_valid_id]"
                        label="Photocopy of Valid Identification Card of Claimant"
                    />
                </div>
                <div class="col">
                    <x-form-image-submission
                        name="burial_assistance[deceased_valid_id]"
                        label="Photocopy of Valid Identification Card of Deceased"
                    />
                </div>
            </div>
        </div>
        <hr>
        <h2>For Muslim Citizens</h2>
        <div
            class="col"
        >
            <x-form-image-submission
                name="burial_assistance[burial_rites]"
                label="Certificate of Burial Rites (signed by IMAM)"
                helpText="From Muslim/Islam Religious Community"
            />
        </div>
        <div
            class="col"
        >
            <x-form-image-submission
                name="burial_assistance[internment_certificate]"
                label="Certificate of Internment"
                helpText="From Muslim/Islam Religious Community"
            />
        </div>
        <hr>
        <div
            class="col mt-4"
        >
            <x-form-image-submission
                name="burial_assistance[proof_of_relationship]"
                label="Proof of Relationship between Claimant and Deceased"
                helpText="From Taguig City Civil Registry or Philippine Statistics Authority (PSA)."
            />
            <ul class="list-group">
                <li class="list-group-item active">Example of documents for Proof of Relationship</li>
                <li class="list-group-item">Marriage Contract (Spouse) - From Taguig City Civil Registry</li>
                <li class="list-group-item">Birth Certificate - From Taguig City Civil Registry</li>
                <li class="list-group-item">Baptismal Certificate (for Siblings, Children, Parents) - Church where the claimant is baptized</li>
            </ul>
        </div>
    </div>
</div>
