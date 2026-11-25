<div class="d-flex flex-column gap-6" data-status-loading="save submit" data-status-loading-dialog="true">
    <x-card>
        <x-slot:header>Walk-in Applicant</x-slot:header>
        <p>Record the applicant without creating a login account. Incomplete information can be saved as a draft.</p>
        @if ($fileserverWarning)
            <div class="alert alert-warning" role="alert">
                <p class="mb-2">{{ $fileserverWarning }}</p>
                <button type="button" class="btn btn-sm btn-light-warning" wire:click="checkFileserver" wire:loading.attr="disabled">Check Again</button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif
        <div class="row g-3">
            @foreach (['first_name' => 'First Name', 'middle_name' => 'Middle Name', 'last_name' => 'Last Name', 'suffix' => 'Suffix'] as $field => $label)
                <div class="col-sm-6 col-lg-3" wire:key="identity-{{ $field }}"><x-form.input :name="'data.identity.'.$field" :label="$label" wire:model="data.identity.{{ $field }}" maxlength="255" /></div>
            @endforeach
            <div class="col-sm-6"><x-form.input name="data.client.dateOfBirth" label="Date of Birth" type="date" wire:model="data.client.dateOfBirth" /></div>
            <div class="col-sm-6"><x-form.input name="data.client.contactNumber" label="Contact Number" wire:model="data.client.contactNumber" maxlength="255" /></div>
            @foreach (['demographic.sexId' => ['Sex', 'sex'], 'demographic.religionId' => ['Religion', 'religion'], 'demographic.nationalityId' => ['Nationality', 'nationality'], 'social.civilId' => ['Civil Status', 'civil'], 'social.educationId' => ['Educational Attainment', 'education'], 'relationshipId' => ['Relationship to Beneficiary', 'relationship']] as $field => [$label, $lookup])
                <div class="col-sm-6 col-lg-4" wire:key="applicant-{{ $field }}"><x-form.select :name="'data.'.$field" :label="$label" :options="$options[$lookup]" :selected="data_get($data, $field)" wire:model="data.{{ $field }}" /></div>
            @endforeach
            @foreach (['philhealth' => 'PhilHealth ID', 'skill' => 'Skills/Occupation', 'income' => 'Estimated Monthly Income'] as $field => $label)
                <div class="col-sm-4" wire:key="social-{{ $field }}"><x-form.input :name="'data.social.'.$field" :label="$label" wire:model="data.social.{{ $field }}" maxlength="255" /></div>
            @endforeach
        </div>
        @include('walk-in.address', ['group' => 'address', 'title' => 'Present Address'])
    </x-card>
    <x-card>
        <x-slot:header>Deceased Beneficiary</x-slot:header>
        <div class="row g-3">
            @foreach (['first_name' => 'First Name', 'middle_name' => 'Middle Name', 'last_name' => 'Last Name', 'suffix' => 'Suffix'] as $field => $label)
                <div class="col-sm-6 col-lg-3" wire:key="beneficiary-{{ $field }}"><x-form.input :name="'data.beneficiary.'.$field" :label="$label" wire:model="data.beneficiary.{{ $field }}" maxlength="255" /></div>
            @endforeach
            @foreach (['date_of_birth' => 'Date of Birth', 'date_of_death' => 'Date of Death'] as $field => $label)
                <div class="col-sm-6" wire:key="beneficiary-{{ $field }}"><x-form.input :name="'data.beneficiary.'.$field" :label="$label" type="date" wire:model="data.beneficiary.{{ $field }}" /></div>
            @endforeach
            @foreach (['sex_id' => ['Sex', 'sex'], 'religion_id' => ['Religion', 'religion']] as $field => [$label, $lookup])
                <div class="col-sm-6" wire:key="beneficiary-{{ $field }}"><x-form.select :name="'data.beneficiary.'.$field" :label="$label" :options="$options[$lookup]" :selected="data_get($data, 'beneficiary.'.$field)" wire:model.live="data.beneficiary.{{ $field }}" /></div>
            @endforeach
            <label class="col-12"><input type="checkbox" wire:model="data.beneficiary.pwd"> Person with disability</label>
        </div>
        @include('walk-in.address', ['group' => 'beneficiaryAddress', 'title' => 'Place of Birth'])
        <h4 class="mt-6">Family Members</h4>
        @foreach ($data['family'] ?? [] as $index => $member)
            <div class="border rounded p-3 mb-3 row g-3" wire:key="family-{{ $index }}">
                @foreach (['name' => 'Name', 'age' => 'Age', 'occupation' => 'Occupation', 'income' => 'Income'] as $field => $label)
                    <div class="col-sm-6 col-lg-3"><x-form.input :name="'data.family.'.$index.'.'.$field" :label="$label" wire:model="data.family.{{ $index }}.{{ $field }}" /></div>
                @endforeach
                @foreach (['sex_id' => ['Sex', 'sex'], 'civil_id' => ['Civil Status', 'civil'], 'relationship_id' => ['Relationship', 'relationship']] as $field => [$label, $lookup])
                    <div class="col-sm-4"><x-form.select :name="'data.family.'.$index.'.'.$field" :label="$label" :options="$options[$lookup]" :selected="$member[$field] ?? null" wire:model="data.family.{{ $index }}.{{ $field }}" /></div>
                @endforeach
                <div><button type="button" class="btn btn-sm btn-light-danger" wire:click="removeFamilyMember({{ $index }})">Remove Member</button></div>
            </div>
        @endforeach
        <button type="button" class="btn btn-sm btn-light-primary" wire:click="addFamilyMember">Add Family Member</button>
        <x-slot:footer>
            <a class="btn btn-sm btn-light" href="{{ route('walk-in.index') }}">Back to Intakes</a>
            <button type="button" class="btn btn-sm btn-primary" wire:click="save" wire:loading.attr="disabled">Save Draft &amp; Review</button>
        </x-slot:footer>
    </x-card>
    @if ($readyForReview)
        <x-card>
            <x-slot:header>Review Before Submission</x-slot:header>
            <p><strong>Applicant:</strong> {{ data_get($data, 'identity.first_name') }} {{ data_get($data, 'identity.last_name') }} · {{ data_get($data, 'client.dateOfBirth') }} · {{ data_get($data, 'client.contactNumber') }}</p>
            <h4>Possible Existing Records</h4>
            <p>These are possible matches, not confirmed duplicates. Records will not be merged automatically.</p>
            @forelse ($duplicates as $duplicate)
                <p wire:key="duplicate-{{ $duplicate->uuid }}"><a href="{{ route('client.show', $duplicate) }}" target="_blank" rel="noopener">{{ $duplicate->fullname() }}</a> · {{ $duplicate->date_of_birth }} · {{ $duplicate->contact_number }}</p>
            @empty
                <p>No possible matches were found among submitted client records.</p>
            @endforelse
            <h4>Supporting Documents</h4>
            @if ($requiresDocuments)
                <p>Upload required images before submission. Successfully saved documents remain with this draft if another upload fails.</p>
            @else
                <p class="text-muted">Images are optional and will not be stored in local/testing because the fileserver mock is disabled.</p>
            @endif
            @foreach ($documents as $document)
                <div class="mb-4" wire:key="document-{{ $document->id }}">
                    <label class="form-label" for="intake-document-{{ $document->id }}">{{ $document->name }} {{ $requiresDocuments && $document->is_required ? '(Required)' : '(Optional)' }}</label>
                    @if (isset(($intake->documents ?? [])[$document->id]))
                        <p class="text-success">Document saved.</p>
                    @else
                        <input class="form-control" id="intake-document-{{ $document->id }}" type="file" accept="image/jpeg,image/png,image/webp" wire:model="images.{{ $document->id }}">
                    @endif
                    @if (isset($images[$document->id]))<button type="button" class="btn btn-sm btn-light mt-2" wire:click="removeImage({{ $document->id }})">Remove New Selection</button>@endif
                    @error('images.'.$document->id)<p class="text-danger">{{ $message }}</p>@enderror
                </div>
            @endforeach
            <label class="d-block mb-4"><input type="checkbox" wire:model="reviewed"> I reviewed the applicant information, supporting evidence, and possible duplicate records.</label>
            <button type="button" class="btn btn-success" wire:click="submit" wire:loading.attr="disabled" @disabled($fileserverWarning !== null)>Submit Walk-in Application</button>
            <span wire:loading class="ms-3" role="status">Please wait…</span>
        </x-card>
    @endif
</div>
