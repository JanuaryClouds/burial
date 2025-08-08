@props([
    'service' => null,
])

<form action="{{ route('admin.burial.store') }}" method="post" enctype="multipart/form-data" id="burialServiceForm" class="row flex-column w-75 mx-auto gap-4">
    @csrf
    @method('post')

    <div class="container mx-auto p-4 gap-4 bg-white shadow rounded-md p-4">
        <header class="row d-flex">
            <span class="d-flex align-items-center gap-2">
                <img src="{{ asset('images/CSWDO.webp') }}" alt="" class="mr-2" style="width: 100px">
                <span class="d-flex flex-column justify-content-center">
                    <h3 class="fw-semibold">Burial Service Form</h3>
                    <p class="text-lg text-gray-600">CSWDO Burial Assistance</p>
                </span>
            </span>
        </header>
        <small class="text-xs">All fields marked by (*) are required</small>
        <hr class="border-1">

        <div class="row row-gap-2">
            <h4 class="text-black">Details of the Deceased</h4>
            <div class="d-flex justify-content-between align-items-start gap-2">
                <span class="d-flex flex-column w-25 justify-content-between">
                    <input type="text" required name="deceased_lastname" id="deceased_lastname" class="form-control">
                    <label for="deceased_lastname" class="form-label text-sm text-center">Last Name*</label>
                </span>
                <span class="d-flex flex-column w-75 justify-content-between">
                    <input type="text" required name="deceased_firstname" id="deceased_firstname" class="form-control">
                    <label for="deceased_firstname" class="text-sm form-label text-center">First Name M.I.*</label>
                </span>
            </div>
            <h4 class="fw-semibold text-black">Representative / Contact Person</h4>
            <div class="d-flex justify-content-start align-items-center w-100 gap-1">
                <span class="d-flex flex-column w-50 justify-content-between">
                    <input type="text" required name="representative" id="representative" class="form-control">
                    <label for="representative" class="text-sm form-label text-center">Full Name*</label>
                </span>
                <span class="d-flex flex-column w-25 justify-content-between">
                    <input type="text" required name="representative_contact" id="representative_contact" class="form-control">
                    <label for="representative_contact" class="text-sm form-label text-center">Contact Details*</label>
                </span>
                <span class="d-flex flex-column w-25 justify-content-between">
                    <select required name="rep_relationship" id="rep_relationship" class="form-select">
                        <option value="">Select Relationship</option>
                        @foreach ($relationships as $relationship)
                            <option value="{{ $relationship->id }}">{{ $relationship->name }}</option>
                        @endforeach
                    </select>
                    <label for="rep_relationship" class="text-sm form-label text-center">Relationship to the Deceased*</label>
                </span>
            </div>
            <hr class="border-2">
            <div class="d-flex flex-column gap-2">
                <h4 class="fw-semibold text-black">Burial Service Details</h4>
                <h5 class="">Address of Burial</h5>
                <div class="d-flex justify-content-between align-items-center w-100 gap-1">
                    <span class="d-flex flex-column w-75 justify-between">
                        <input type="text" required name="burial_address" id="burial_address" class="form-control">
                        <label for="burial_address" class="text-sm form-label text-center">Building Number, House No., Street*</label>
                    </span>
                    <span class="d-flex flex-column w-25 justify-content-between">
                        <select name="barangay_id" id="barangay_id" required class="form-select">
                            <option value="">Select Barangay</option>
                            @foreach ($barangays as $barangay)
                                <option value="{{ $barangay->id }}">{{ $barangay->name }}</option>
                            @endforeach
                        </select>
                        <label for="barangay_id" class="form-label text-center">Barangay*</label>
                    </span>
                </div>
                <div class="d-flex justify-content-start align-items-center w-100 gap-4">
                    <h5 class="mb-0">Date of Burial</h5>
                    <span class="d-flex justify-content0between align-items-center gap-1">
                        <label for="start_of_burial" class="form-label text-center mb-0">Start*</label>
                        <input type="date" required name="start_of_burial" id="start_of_burial" class="form-control">
                    </span>
                    <span class="d-flex justify-content-between align-items-center gap-1">
                        <label for="end_of_burial" class="form-label text-center mb-0">End*</label>
                        <input type="date" required name="end_of_burial" id="end_of_burial" class="form-control">
                    </span>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center w-full gap-2">
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
                            required
                            class="form-control"
                        />
                        <span class="input-group-text">.00</span>
                    </span>
                    <label for="collected_funds" class="form-label text-center">Collected Funds*</label>
                </span>
            </div>

            <div class="d-flex justify-content-between align-items-center w-full gap-2">
                <span class="d-flex flex-column justify-content-between w-75">
                    <input type="text" readonly name="burial_service_address" id="burial_service_address"
                    disabled class="form-control">
                    <label for="burial_service_address" class="form-label text-center">Address of Burial Service</label>
                </span>
                <span class="d-flex flex-column justify-content-between w-25">
                    <input type="text" name="burial_service_contact" id="burial_service_contact" readonly disabled class="form-control">
                    <label for="burial_service_contact" class="form` text-center">Contact Details</label>
                </span>
            </div>
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
                    <textarea rows="4" name="remarks" class="form-control"></textarea>
                </span>
            </div>
        </div>
    </div>

    <span class="d-flex justify-content-center align-items-center gap-2">
        <button type="submit" class="btn btn-primary">
            Submit
        </button>
        <button type="reset" class="btn btn-secondary">
            Clear
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

    providerSelect.addEventListener('change', (e) => {
        const selectedOption = e.target.options[e.target.selectedIndex];
        const address = selectedOption.getAttribute('data-address') || '';
        const barangay = selectedOption.getAttribute('data-barangay') || '';
        const contact = selectedOption.getAttribute('data-contact') || '';

        addressInput.value = address + ", " + barangay;
        contactInput.value = contact;
    });
</script>