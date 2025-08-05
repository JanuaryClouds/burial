@props(['serviceRequest' => null])

<form action="{{ route('burial.request.store') }}" method="POST" class="row w-75" enctype="multipart/form-data">
    @csrf
    <div class="container mx-auto p-4 bg-white shadow-lg rounded-md">
        <header class="row d-flex">
            <span class="d-flex align-middle gap-2">
                <img src="{{ asset('images/CSWDO.webp') }}" alt="" class="mr-2" style="width: 100px">
                <span class="d-flex flex-column justify-content-center">
                    <h3 class="fw-semibold">Burial Service Form</h3>
                    <p class="text-lg text-gray-600">CSWDO Burial Assistance</p>
                </span>
            </span>
        </header>
        <p class="text-gray-400 text-sm">All fields marked by (*) are required. By submitting this form, you agree to providing CSDWO of Taguig City the information required for the burial assistance.</p>
        <hr class="border-2 border-gray-700">

        <div class="d-grid row-gap-2">
            <h4 class="fw-semibold text-black">Details of the Deceased</h4>
            <div class="d-flex justify-content-between align-items-start gap-2">
                <span class="d-flex flex-column w-25 justify-content-between">
                    <input type="text" required name="deceased_lastname" id="deceased_lastname" class="form-control" {{ $serviceRequest ? 'readonly disabled' : '' }} value="{{ $serviceRequest ? $serviceRequest->deceased_lastname : '' }}">
                    <label for="deceased_lastname" class="form-label text-sm text-center">Last Name*</label>
                </span>
                <span class="d-flex flex-column w-75 justify-content-between">
                    <input type="text" required name="deceased_firstname" id="deceased_firstname" class="form-control" {{ $serviceRequest ? 'readonly disabled' : '' }} value="{{ $serviceRequest ? $serviceRequest->deceased_firstname : '' }}">
                    <label for="deceased_firstname" class="form-label text-sm text-center">First Name M.I.*</label>
                </span>
            </div>
            <h4 class="fw-semibold text-black">Representative / Contact Person</h4>
            <span class="d-flex flex-column gap-2 justify-content-between align-items-start">
                <p class="text-sm">Please state the name and contact details of the person who will be the representative/contact person in the burial.</p>
            </span>
            <div class="d-flex justify-content-start align-items-center w-100 gap-2">
                <span class="d-flex flex-column w-50 justify-content-between">
                    <input type="text" required name="representative" id="representative" class="form-control"
                    {{ $serviceRequest ? 'readonly disabled' : '' }} value="{{ $serviceRequest ? $serviceRequest->representative : '' }}">
                    <label for="representative" class="form-label text-sm text-center">Full Name*</label>
                </span>
                <span class="d-flex flex-column w-25 justify-content-between">
                    <input type="text" required name="representative_contact" id="representative_contact" class="form-control" 
                    {{ $serviceRequest ? 'readonly disabled' : '' }} value="{{ $serviceRequest ? $serviceRequest->representative_contact : '' }}">
                    <label for="representative_contact" class="form-label text-sm text-center">Contact Details*</label>
                </span>

                <span class="d-flex flex-column w-25 justify-content-between">
                    <select required name="rep_relationship" id="rep_relationship" class="form-select"
                    {{ $serviceRequest ? 'readonly disabled' : '' }}>
                        @if ($serviceRequest)
                            <option value="{{ $serviceRequest->rep_relationship }}">{{ $relationships->firstWhere('id', $serviceRequest->rep_relationship)->name ?? 'Unknown' }}</option>
                        @else
                            <option value="">Select Relationship*</option>
                        @endif
                        @foreach ($relationships as $relationship)
                            <option value="{{ $relationship->id }}">{{ $relationship->name }}</option>
                        @endforeach
                    </select>
                    <label for="rep_relationship" class="form-label text-sm text-center">Relationship to the Deceased*</label>
                </span>
            </div>
            <hr class="border-2 border-gray-700 border-dashed">
            <div class="d-flex flex-column gap-2">
                <span class="d-flex flex-column gap-1">
                    <h4 class="fw-semibold text-black">Burial Service Details</h4>
                    <p class="text-sm">Please state where and when the burial shall be taken place.</p>
                </span>
                <h5>Address of Burial</h5>
                <div class="d-flex justify-content-between align-items-center w-100 gap-2">
                    <span class="d-flex flex-column w-75 justify-content-between">
                        <input type="text" required name="burial_address" id="burial_address" class="form-control"
                        {{ $serviceRequest ? 'readonly disabled' : '' }} value="{{ $serviceRequest ? $serviceRequest->burial_address : '' }}">
                        <label for="burial_address" class="form-label text-center">Building Number, House No., Street*</label>
                    </span>
                    <span class="d-flex flex-column w-25 justify-content-between">
                        <select name="barangay_id" id="barangay_id" class="form-select"
                        {{ $serviceRequest ? 'readonly disabled' : '' }}>
                            @if ($serviceRequest)
                                <option value="{{ $serviceRequest->barangay_id }}">{{ $barangays->firstWhere('id', $serviceRequest->barangay_id)->name ?? 'Unknown' }}</option>
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
                <div class="d-flex align-items-center w-100 gap-4">
                    <h5 class="mr-4">Date of Burial</h5>
                    <span class="d-flex justify-content-between align-items-center gap-2">
                        <label for="start_of_burial" class="form-label text-center">Start*</label>
                        <input type="date" required name="start_of_burial" id="start_of_burial" class="form-control"
                        {{ $serviceRequest ? 'hidden' : '' }}>
                        <p class="text-sm text-gray-700 pt-3 fw-semibold {{ $serviceRequest ? '' : 'hidden' }}">{{ $serviceRequest ? Str::limit($serviceRequest->start_of_burial, 10) : '' }}</p>
                    </span>
                    <span class="d-flex justify-content-between align-items-center gap-2">
                        <label for="end_of_burial" class="form-label text-center">End*</label>
                        <p class="text-sm text-gray-700 pt-3 fw-semibold {{ $serviceRequest ? '' : 'hidden' }}">{{ $serviceRequest ? Str::limit($serviceRequest->end_of_burial, 10) : '' }}</p>
                        <input type="date" required name="end_of_burial" id="end_of_burial" class="form-control"
                        {{ $serviceRequest ? 'hidden' : '' }}>
                    </span>
                </div>
            </div>
        </div>
        <hr class="border-2 border-dashed border-gray-700">
        <div class="d-flex flex-column gap-2">
            <span class="d-flex flex-column gap-1">
                <h4 class="fw-semibold">Certificate of Death</h4>
                <p class="text-sm">Please upload a clear copy of the Certificate of Death. It must include the first and second page.</p>
            </span>
            <div class="d-flex justify-content-between align-items-start w-100 gap-2">
                <input 
                    type="file" 
                    name="images[]" 
                    id="images"
                    accept="image/*"
                    multiple
                    required
                    onchange="limitFiles(this)"
                    {{ $serviceRequest ? 'readonly disabled' : '' }}
                    class="form-control {{ $serviceRequest ? 'd-none' : '' }}"
                    >
                <p class="text-sm text-gray-400 {{ $serviceRequest ? 'd-block' : 'd-none' }}">Certificate of Death has been uploaded</p>
            </div>
        </div>
        <hr class="border-2 border-dashed border-gray-700">
        <div class="d-flex flex-column gap-2">
            <h4 class="fw-semibold">Remarks</h4>
            <div class="d-flex justify-content-between align-items-start w-100 gap-1">
                <span class="d-flex flex-column w-100 justify-content-between">
                    <textarea rows="4" name="remarks" class="form-control"
                    {{ $serviceRequest ? 'readonly disabled' : '' }}>{{ $serviceRequest ? $serviceRequest->remarks : '' }}</textarea>
                </span>
            </div>
        </div>

        
        @if (!$serviceRequest)
        
        <!-- Confirmation Modal -->
        <!-- Button trigger modal -->
        <div
            class="col d-flex w-100 mt-4 justify-content-center align-items-center gap-2"
        >
            <button
                type="button"
                class="btn btn-primary"
                data-bs-toggle="modal"
                data-bs-target="#confirmationSubmit"
                >
                Submit
            </button>
            <button type="reset" class="btn btn-secondary">
                Clear
            </button>
        </div>
    <!-- Modal -->
    <div
        class="modal fade"
        id="confirmationSubmit"
        tabindex="-1"
        role="dialog"
        aria-labelledby="modalTitleId"
        aria-hidden="true"
        >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitleId">
                        Confirm Submission
                    </h5>
                    <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                    aria-label="Close"
                    ></button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div
                        class="row justify-content-start align-items-start g-2"
                        >
                        <p>Are you certain you want to submit this request? Please ensure all the information you have provided are factual and accurate to the death certificate you provided. Failure to provide correct information will result in the rejection of your request.</p>
                        <p class="fw-semibold">Note: You cannot edit or change the information after submitting the request</p>
                    </div>
                    
                    </div>
                </div>
                <div class="modal-footer">
                    <button
                    type="button"
                    class="btn btn-secondary"
                    data-bs-dismiss="modal"
                    >
                    Close
                </button>
                    <button type="submit" class="btn btn-primary">
                        Confirm Submission
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        var modalId = document.getElementById('confirmationSubmit');
        
        modalId.addEventListener('show.bs.modal', function (event) {
            // Button that triggered the modal
            let button = event.relatedTarget;
            // Extract info from data-bs-* attributes
            let recipient = button.getAttribute('data-bs-whatever');
            
            // Use above variables to manipulate the DOM
        });

        function limitFiles(input) {
            if (input.files && input.files.length > 2) {
                alert("You can only upload two file at a time.");
                input.value = "";
            }
        }
        </script>
    <span class="d-flex gap-1 justify-content-center mt-3 {{ $serviceRequest ? 'hidden' : '' }}">
        <p class="text-gray-400 text-sm">If you have submitted a request before, please track it instead using the</p>
        <a href="/" class="text-blue-500 hover:underline text-sm pt-1">Tracker</a>
        <p class="text-gray-400 text-sm">from the landing page.</p>
    </span>
    @endif
</div>
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
</script>