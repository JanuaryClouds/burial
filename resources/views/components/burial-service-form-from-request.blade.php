@props(['serviceRequest' => null])
<form action="{{ route('admin.burial.request.to.service.store', ['uuid' => $serviceRequest->uuid]) }}" method="post" enctype="multipart/form-data" id="burialServiceForm" class="d-flex flex-column gap-4 w-75 mx-auto">
    @csrf
    @method('post')

    <div class="container mx-auto p-4 gap-4 bg-white shadow rounded-md">
        <header class="row d-flex">
            <span class="d-flex items-center-center gap-2">
                <img src="{{ asset('images/CSWDO.webp') }}" alt="" class="mr-2" style="width: 100px;">
                <span class="d-flex flex-column justify-content-center">
                    <h3 class="fw-semibold">Burial Service Form</h3>
                    <p class="mb-0">CSWDO Burial Assistance</p>
                </span>
            </span>
        </header>
        <small class="text-xs">All fields marked by (*) are required</small>
        <hr class="border-1">

        <div class="row row-gap-2">
            <h4 class="text-black">Details of the Deceased</h4>
            <div class="d-flex justify-content-between align-items-start gap-2">
                <span class="d-flex flex-column w-25 justify-content-between">
                    <input type="text" readonly value="{{ $serviceRequest->deceased_lastname }}" name="deceased_lastname" id="deceased_lastname" class="form-control">
                    <label for="deceased_lastname" class="form-label text-center">Last Name*</label>
                </span>
                <span class="d-flex flex-column w-75 justify-content-between">
                    <input type="text" readonly value="{{ $serviceRequest->deceased_firstname }}" name="deceased_firstname" id="deceased_firstname" class="form-control">
                    <label for="deceased_firstname" class="form-label text-center">First Name*</label>
                </span>
            </div>
            <h4 class="text-black">Representative / Contact Person</h4>
            <div class="d-flex justify-content-between align-items-center w-100 gap-1">
                <span class="d-flex flex-column w-25 justify-content-between">
                    <input type="text" readonly value="{{ $serviceRequest->representative }}" name="representative" id="representative" class="form-control">
                    <label for="representative" class="form-label text-center">Full Name*</label>
                </span>
                <span class="d-flex flex-column w-25 justify-content-between">
                    <input type="text" readonly value="{{ $serviceRequest->representative_phone }}" name="representative_phone" id="representative_phone" class="form-control">
                    <label for="representative_phone" class="form-label text-center">Phone (Mobile / Landline)*</label>
                </span>
                <span class="d-flex flex-column w-25 justify-content-between">
                    <input type="text" readonly value="{{ $serviceRequest?->representative_email ?? 'N/A' }}" name="representative_email" id="representative_email" class="form-control">
                    <label for="representative_email" class="form-label text-center">Email</label>
                </span>
                <span class="d-flex flex-column w-25 justify-content-between">
                    <select required name="representative_relationship" id="representative_relationship" class="form-select"
                    {{ $serviceRequest ? 'readonly disabled' : '' }}>
                        @if ($serviceRequest)
                            <option value="{{ $serviceRequest->representative_relationship }}">{{ $relationships->firstWhere('id', $serviceRequest->representative_relationship)->name ?? 'Unknown' }}</option>
                        @else
                            <option value="">Select Relationship*</option>
                        @endif
                        @foreach ($relationships as $relationship)
                            <option value="{{ $relationship->id }}">{{ $relationship->name }}</option>
                        @endforeach
                    </select>
                    <label for="representative_relationship" class="form-label text-center">Relationship to the Deceased*</label>
                </span>
            </div>
            <hr class="border-2">

            <div class="d-flex flex-column gap-2">
                <h4 class="text-black">Burial Service Details</h4>
                <h5 class="">Address of Burial</h5>
                <div class="d-flex justify-content-between align-items-center w-100 gap-1">
                    <span class="d-flex flex-column w-75 justify-content-between">
                        <input type="text" readonly value="{{ $serviceRequest->burial_address }}" name="burial_address" id="burial_address" class="form-control">
                        <label for="burial_address" class="form-label text-center">Building Number, House No., Street*</label>
                    </span>
                    <span class="d-flex flex-column w-25 justify-content-between">
                        <select name="barangay_id" id="barangay_id" required class="form-select"
                        @if ($serviceRequest)
                        readonly
                        style="pointer-events: none; background-color: #f9f9f9;"
                        @endif
                        >
                            @if ($serviceRequest)
                                <option 
                                    value="{{ $serviceRequest->barangay_id }}"
                                    >{{ $barangays->firstWhere('id', $serviceRequest->barangay_id)->name ?? 'Unknown' }}</option>
                            @else
                                <option value="">Select Barangay*</option>
                            @endif
                            @foreach ($barangays as $barangay)
                                <option value="{{ $barangay->id }}">{{ $barangay->name }}</option>
                            @endforeach
                        </select>
                        <label for="barangay_id" class="form-label text-center">Barangay*</label>
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center w-100 gap-4">
                    <h5 class="mb-0">Date of Burial</h5>
                    <!-- TODO: Error: The invalid form control with name=‘start_of_burial’ is not focusable. -->
                    <span class="d-flex justify-content-between align-items-baseline gap-1">
                        <label for="start_of_burial" class="form-label text-center">Start*</label>
                        >
                        <p class="px-2 py-2 border rounded">{{ $serviceRequest ? $serviceRequest->start_of_burial : '' }}</p>
                    </span>
                    <span class="d-flex justify-content-between align-items-baseline gap-1">
                        <label for="end_of_burial" class="form-label text-center">End*</label>
                        >
                        <p class="px-2 py-2 border rounded">{{ $serviceRequest ? $serviceRequest->end_of_burial : '' }}</p>
                    </span>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center w-100 gap-2">
                <span class="d-flex flex-column justify-content-between w-75">
                    <select name="burial_service_provider" required id="burial_service_provider" class="form-select">
                        <option value="">Select Provider</option>
                        @foreach ($providers as $provider)
                            <option
                                value="{{ $provider->id }}"
                                data-address="{{ $provider->address }}"
                                data-contact="{{ $provider->contact_details }}"
                                data-barangay="{{ $provider->barangay->name }}"
                            >
                                {{ $provider->name }}
                                @if ($serviceRequest->barangay != $provider->barangay)
                                    <p class="text-danger">(Note: Different barangay)</p>
                                @endif
                            </option>
                        @endforeach
                    </select>
                    <label for="burial_service_provider" class="form-label text-center">Burial/Funeral Service*</label>
                </span>
                <span class="d-flex flex-column w-25 justify-content-between">
                    <span class="input-group">
                        <span class="input-group-text">₱</span>
                        <input
                        type="text"
                        name="collected_funds"
                        id="collected_funds"
                        value=""
                        required
                        class="form-control text-end"
                        />
                        <span class="input-group-text">.00</span>
                        <label for="collected_funds" class="form-label text-center">Collected Funds*</label>
                    </span>
                </span>
            </div>

            <div class="d-flex justify-content-between align-items-center w-100 gap-2">
                <span class="d-flex flex-column justify-content-between w-75">
                    <input type="text" readonly name="burial_service_address" id="burial_service_address"
                    disabled class="form-control">
                    <label for="burial_service_address" class="form-label text-center">Address of Burial Service</label>
                </span>
                <span class="d-flex flex-column justify-content-between w-25">
                    <input type="text" name="burial_service_contact" id="burial_service_contact" readonly disabled class="form-control">
                    <label for="burial_service_contact" class="form-label text-center">Contact Details</label>
                </span>
            </div>
            @if (session()->has('error'))
                <div
                    class="alert alert-danger"
                    role="alert"
                >
                    <strong><i class="fa-solid fa-warning"></i></strong> {{ session('error') }}
                </div>
            @endif
        </div>

        <hr class="border-2">
        <div class="d-flex flex-column gap-1">
            <h4 class="fw-semibold">Images*</h4>
            <input 
                type="file" 
                name="images[]" 
                id="images"
                accept="image/*"
                multiple
                required
                class="form-control"
                >
        </div>
        <hr class="border-2">
        <div class="d-flex flex-column gap-2">
            <h4 class="fw-semibold">Remarks</h4>
            <div class="d-flex justify-content-between align-items-start w-100 gap-1">
                <span class="d-flex flex-column w-100 justify-content-between">
                    <textarea rows="4" name="provider_remarks" class="form-control"></textarea>
                </span>
            </div>
        </div>
    </div>

    <span class="d-flex justify-content-center align-items-center gap-2">
        <button type="submit" class="btn btn-primary">
            Submit
        </button>
        <button type="reset" class="btn btn-secondary">
            clear
        </button>
        <a href="{{ route('admin.burial.history') }}" class="btn btn-outline-primary">
            Cancel
        </a>
    </span>

</form>

<script>
    const inputFields = document.querySelectorAll('input');
    const submitButton = document.querySelector('button[type="submit"]');

    function checkFields() {
        let allFilled = true;
        inputFields.forEach(field => {
            if (field.value.trim() === '') {
                allFilled = false;
            }
        });
        if (allFilled) {
            submitButton.disabled = false;
        } else {
            submitButton.disabled = true;
        }
    }

    inputFields.forEach(field => {
        field.addEventListener('input', checkFields);
    });

    checkFields();

    const providerSelect = document.getElementById('burial_service_provider');
    const addressInput = document.getElementById('burial_service_address');
    const contactInput = document.getElementById('burial_service_contact');
    const warningText = document.getElementById('provider_warning');
    const providerWarningIcon = document.getElementById('provider_warning_icon');

    providerSelect.addEventListener('change', (e) => {
        const selectedOption = e.target.options[e.target.selectedIndex];
        const address = selectedOption.getAttribute('data-address') || '';
        const barangay = selectedOption.getAttribute('data-barangay') || '';
        const contact = selectedOption.getAttribute('data-contact') || '';
        
        addressInput.value = address + ", " + barangay;
        contactInput.value = contact;
    });

</script>