<form action="{{ route('admin.burial.store') }}" method="post" enctype="multipart/form-data" id="burialServiceForm" class="flex flex-col items-center gap-12">
    @csrf
    @method('post')

    <div class="flex flex-col gap-4 bg-white shadow-lg rounded-md p-4 mx-36">
        <header class="flex justify-between">
            <span class="flex justify-between items-center gap-2">
                <img src="{{ asset('images/CSWDO.webp') }}" alt="" class="w-24 mr-2">
                <span class="flex flex-col gap-2">
                    <h3 class="text-4xl font-semibold">Burial Service Form</h3>
                    <p class="text-lg text-gray-600">CSWDO Burial Assistance</p>
                </span>
            </span>
        </header>
        <p class="text-gray-400 text-xs">All fields marked by (*) are required</p>
        <hr class="border-2 border-gray-700">

        <div class="flex flex-col gap-4">
            <h4 class="text-lg font-semibold">Details of the Deceased</h4>
            <div class="flex justify-between items-start w-full gap-2">
                <span class="flex flex-col w-1/3 justify-between">
                    <input type="text" required name="deceased_lastname" id="deceased_lastname" class="border-2 border-gray-300 rounded-md px-2 py-1">
                    <label for="deceased_lastname" class="text-sm text-gray-400 text-center">Last Name*</label>
                </span>
                <span class="flex flex-col w-2/3 justify-between">
                    <input type="text" required name="deceased_firstname" id="deceased_firstname" class="border-2 border-gray-300 rounded-md px-2 py-1">
                    <label for="deceased_firstname" class="text-sm text-gray-400 text-center">First Name*</label>
                </span>
            </div>
            <h4 class="text-lg font-semibold">Representative / Contact Person</h4>
            <div class="flex justify-start items-center w-full gap-2">
                <span class="flex flex-col w-1/3 justify-between">
                    <input type="text" required name="representative" id="representative" class="border-2 border-gray-300 rounded-md px-2 py-1">
                    <label for="representative" class="text-sm text-gray-400 text-center">Full Name*</label>
                </span>
                <span class="flex flex-col w-1/3 justify-between">
                    <input type="text" required name="representative_contact" id="representative_contact" class="border-2 border-gray-300 rounded-md px-2 py-1">
                    <label for="representative_contact" class="text-sm text-gray-400 text-center">Contact Details*</label>
                </span>

                <!-- NOTE: Can be a dropdown menu -->
                <span class="flex flex-col w-1/3 justify-between">
                    <select required name="rep_relationship" id="rep_relationship" class="border-2 border-gray-300 rounded-md px-2 py-1">
                        <option value="">Select Relationship</option>
                        @foreach ($relationships as $relationship)
                            <option value="{{ $relationship->id }}">{{ $relationship->name }}</option>
                        @endforeach
                    </select>
                    <label for="rep_relationship" class="text-sm text-gray-400 text-center">Relationship to the Deceased*</label>
                </span>
            </div>
            <hr class="border-2 border-dashed border-gray-700">

            <div class="flex flex-col gap-2">
                <h4 class="text-lg font-semibold">Burial Service Details</h4>
                <h5 class="">Address of Burial</h5>
                <div class="flex justify-between items-center w-full gap-2">
                    <span class="flex flex-col w-4/6 justify-between">
                        <input type="text" required name="burial_address" id="burial_address" class="border-2 border-gray-300 rounded-md px-2 py-1">
                        <label for="burial_address" class="text-sm text-gray-400 text-center">Building Number, House No., Street*</label>
                    </span>
                    <span class="flex flex-col w-2/6 justify-between">
                        <select name="barangay_id" id="barangay_id" class="border-2 border-gray-300 rounded-md px-2 py-1">
                            <option value="">Select Barangay</option>
                            @foreach ($barangays as $barangay)
                                <option value="{{ $barangay->id }}">{{ $barangay->name }}</option>
                            @endforeach
                        </select>
                        <label for="barangay_id" class="text-sm text-gray-400 text-center">Barangay*</label>
                    </span>
                </div>
                <div class="flex justify-between items-center w-full gap-2">
                    <h5 class="">Date of Burial</h5>
                    <span class="flex justify-between items-center gap-2">
                        <label for="start_of_burial" class="text-sm text-gray-400 text-center">Start*</label>
                        <input type="date" required name="start_of_burial" id="start_of_burial" class="border-2 border-gray-300 rounded-md px-2 py-1">
                    </span>
                    <span class="flex justify-between items-center gap-2">
                        <label for="end_of_burial" class="text-sm text-gray-400 text-center">End*</label>
                        <input type="date" required name="end_of_burial" id="end_of_burial" class="border-2 border-gray-300 rounded-md px-2 py-1">
                    </span>
                </div>
            </div>

            <div class="flex justify-between items-center w-full gap-2">
                <span class="flex flex-col justify-between w-4/5">
                    <select name="burial_service_provider" required id="burial_service_provider" class="border-2 border-gray-300 rounded-md px-2 py-1">
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
                    <label for="burial_service_provider" class="text-sm text-gray-400 text-center">Burial/Funeral Service*</label>
                </span>
                <span class="flex flex-col w-1/5 justify-between">
                    <input
                        type="text"
                        name="collected_funds"
                        id="collected_funds"
                        value="₱"
                        required
                        oninput="this.value = this.value.replace(/[^₱\d.]/g, '').replace(/^([^₱])/, '₱$1')"
                        class="w-full border-2 border-gray-300 rounded-md px-2 py-1"
                        />
                    <label for="collected_funds" class="text-sm text-gray-400 text-center">Collected Funds*</label>
                </span>
            </div>

            <div class="flex justify-between items-center w-full gap-2">
                <span class="flex flex-col justify-between w-4/5">
                    <input type="text" readonly name="burial_service_address" id="burial_service_address"
                    disabled class="border-2 border-gray-200 bg-gray-200 rounded-md px-2 py-1">
                    <label for="burial_service_address" class="text-sm text-gray-400 text-center">Address of Burial Service</label>
                </span>
                <span class="flex flex-col justify-between w-2/5">
                    <input type="text" name="burial_service_contact" id="burial_service_contact" readonly disabled class="border-2 border-gray-200 bg-gray-200 rounded-md px-2 py-1">
                    <label for="burial_service_contact" class="text-sm text-gray-400 text-center">Contact Details</label>
                </span>
            </div>
        </div>

        <hr class="border-2 border-dashed border-gray-700">
        <div class="flex flex-col gap-2">
            <h4 class="text-lg font-semibold">Images*</h4>
            <input 
                type="file" 
                name="images[]" 
                id="images"
                accept="image/*"
                multiple
                required
                class="w-full border-2 border-gray-300 rounded-md px-2 py-1"
                >
        </div>
        <hr class="border-2 border-dashed border-gray-700">
        <div class="flex flex-col gap-4">
            <h4 class="text-lg font-semibold">Remarks</h4>
            <div class="flex justify-between items-start w-full gap-2">
                <span class="flex flex-col w-full justify-between">
                    <textarea rows="4" name="provider_remarks" class="border-2 border-gray-300 rounded-md px-2 py-1"></textarea>
                </span>
            </div>
        </div>
    </div>

    <span class="flex justify-center items-center gap-4">
        <button type="submit" class="px-4 py-2 text-white font-semibold tracking-widest uppercase text-sm bg-yellow-400 rounded-lg hover:bg-yellow-500 hover:text-black transition-colors">
            Submit
        </button>
        <button type="reset" class="px-4 py-2 text-black font-semibold tracking-widest uppercase text-sm bg-gray-100 rounded-lg hover:bg-gray-300 transition-colors">
            clear
        </button>
        <a href="{{ route('admin.burial.history') }}" class="px-4 py-2 text-white font-semibold tracking-widest uppercase text-sm bg-gray-700 rounded-lg hover:bg-gray-300 hover:text-black transition-colors">
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