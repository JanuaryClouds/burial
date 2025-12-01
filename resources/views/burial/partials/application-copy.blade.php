<div class="card">
    <div class="card-header collapsible cursor-pointer rotate" data-bs-toggle="collapse"
        data-bs-target="#application-form">
        <h4 class="card-title">Copy of Application Form</h4>
    </div>
    <div class="card-body collapse" id="application-form">
        <div class="d-flex flex-column gap-6">
            <span class="d-flex flex-column gap-3">
                <h1>Submitted Application</h1>
                <p>
                    This is a copy of the burial assistance application. All fields in this copy are not editable
                    and
                    fixed.
                    This allows you to track the progress of your application. Some text have to be hidden for
                    privacy.
                </p>
            </span>
            <x-swa-form :application="$burialAssistance" :disabled="true" :readonly="true" />
            <x-deceased-form :deceased="$burialAssistance->deceased" disabled="true" readonly="true" />
            <x-claimant-form :claimant="$burialAssistance->claimant" disabled="true" readonly="true" />
            <x-burial-assistance-details-form :burialAssistance="$burialAssistance" disabled="true" readonly="true" />
        </div>
    </div>
</div>
