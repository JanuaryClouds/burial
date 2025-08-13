@extends('layouts.admin')
@section('content')
<title>{{ $service->deceased_firstname }} {{ $service->deceased_lastname }}'s Burial Service</title>

<div
    class="container-fluid flex-column align-items-center gap-4"
>
    <div class="row col">
        <form action="#" method="post" enctype="multipart/form-data" id="burialServiceForm" class="row flex-column w-75 mx-auto gap-4">
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
                            <input type="text" readonly value="{{ $service->deceased_lastname }}" name="deceased_lastname" id="deceased_lastname" class="form-control" disabled>
                            <label for="deceased_lastname" class="form-label text-sm text-center">Last Name*</label>
                        </span>
                        <span class="d-flex flex-column w-75 justify-content-between">
                            <input type="text" disabled value="{{ $service->deceased_firstname }}" name="deceased_firstname" id="deceased_firstname" class="form-control">
                            <label for="deceased_firstname" class="text-sm form-label text-center">First Name M.I.*</label>
                        </span>
                    </div>
                    <h4 class="fw-semibold text-black">Representative / Contact Person</h4>
                    <div class="d-flex justify-content-start align-items-center w-100 gap-1">
                        <span class="d-flex flex-column w-50 justify-content-between">
                            <input type="text" disabled value="{{ $service->representative }}" name="representative" id="representative" class="form-control">
                            <label for="representative" class="text-sm form-label text-center">Full Name*</label>
                        </span>
                        <span class="d-flex flex-column w-25 justify-content-between">
                            <input type="text" disabled value="{{ $service->representative_contact }}" name="representative_contact" id="representative_contact" class="form-control disabled">
                            <label for="representative_contact" class="text-sm form-label text-center">Contact Details*</label>
                        </span>
                        <span class="d-flex flex-column w-25 justify-content-between">
                            <select required name="rep_relationship" id="rep_relationship" class="form-select"
                            {{ $service ? 'readonly disabled' : '' }}>
                                @if ($service)
                                    <option value="{{ $service->rep_relationship }}">{{ $relationships->firstWhere('id', $service->rep_relationship)->name ?? 'Unknown' }}</option>
                                @else
                                    <option value="">Select Relationship*</option>
                                @endif
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
                                <input type="text" disabled value="{{ $service->burial_address }}" name="burial_address" id="burial_address" class="form-control">
                                <label for="burial_address" class="text-sm form-label text-center">Building Number, House No., Street*</label>
                            </span>
                            <span class="d-flex flex-column w-25 justify-content-between">
                                <select name="barangay_id" id="barangay_id" class="form-select"
                                {{ $service ? 'readonly disabled' : '' }}>
                                    @if ($service)
                                        <option value="{{ $service->barangay_id }}">{{ $barangays->firstWhere('id', $service->barangay_id)->name ?? 'Unknown' }}</option>
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
                                {{ $service ? 'hidden' : '' }}>
                                <p class="text-sm text-gray-700 pt-3 fw-semibold {{ $service ? '' : 'hidden' }}">{{ $service ? Str::limit($service->start_of_burial, 10) : '' }}</p>
                            </span>
                            <span class="d-flex justify-content-between align-items-center gap-2">
                                <label for="end_of_burial" class="form-label text-center">End*</label>
                                <p class="text-sm text-gray-700 pt-3 fw-semibold {{ $service ? '' : 'hidden' }}">{{ $service ? Str::limit($service->end_of_burial, 10) : '' }}</p>
                                <input type="date" required name="end_of_burial" id="end_of_burial" class="form-control"
                                {{ $service ? 'hidden' : '' }}>
                            </span>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center w-full gap-2">
                        <span class="d-flex flex-column justify-content-between w-75">
                            <select name="burial_service_provider" disabled id="burial_service_provider" class="form-select">
                                @if ($service)
                                    <option
                                        value="{{ $service->burial_service_provider }}"
                                        data-address="{{ $providers->firstWhere('id', $service->burial_service_provider)->address ?? '' }}"
                                        data-contact="{{ $providers->firstWhere('id', $service->burial_service_provider)->contact_details ?? '' }}"
                                        data-barangay="{{ $providers->firstWhere('id', $service->burial_service_provider)->barangay->name ?? '' }}"
                                        >
                                        {{ $providers->firstWhere('id', $service->burial_service_provider)->name ?? 'Unknown' }}
                                    </option>
                                @else
                                    <option value="">Select Provider*</option>
                                @endif
                                @foreach ($providers as $provider)
                                    <option value="{{ $provider->id }}">{{ $provider->name }}</option>
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
                                    disabled
                                    value="{{ $service ? $service->collected_funds : '' }}"
                                    class="form-control"
                                />
                                <span class="input-group-text">.00</span>
                            </span>
                            <label for="collected_funds" class="form-label text-center">Collected Funds*</label>
                        </span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center w-full gap-2">
                        <span class="d-flex flex-column justify-content-between w-75">
                            <input type="text" name="burial_service_address" id="burial_service_address"
                            disabled value="{{ $service ? $service->provider->address : '' }}, {{ $service ? $service->provider->barangay->name : '' }}" class="form-control">
                            <label for="burial_service_address" class="form-label text-center mb-0">Address of Burial Service</label>
                        </span>
                        <span class="d-flex flex-column justify-content-between w-25">
                            <input type="text" value={{ $service? $service->provider->contact_details : '' }} name="burial_service_contact" id="burial_service_contact" disabled class="form-control">
                            <label for="burial_service_contact" class="form` text-center">Contact Details</label>
                        </span>
                    </div>
                </div>

                <hr class="border-2">
                <div class="d-flex flex-column gap-1">
                    <h4 class="fw-semibold">Images*</h4>
                    @foreach ($serviceImages as $image)
                        <img src="{{ Storage::url($image) }}" class="rounded">            
                    @endforeach
                </div>
                <hr class="border-2">
                <div class="d-flex flex-column gap-2">
                    <h4 class="fw-semibold">Remarks</h4>
                    <div class="d-flex justify-content-between align-items-start w-100 gap-1">
                        <span class="d-flex flex-column w-100 justify-content-between">
                            <textarea rows="4" disabled name="remarks" class="form-control">{{ $service->remarks }}</textarea>
                        </span>
                    </div>
                </div>
            </div>
        </form>            
    </div>
    <div
        class="row justify-content-center align-items-center g-2 mt-3"
    >
        <div
            class="col d-flex justify-content-center"
        >
            <a
                name=""
                id=""
                target="_blank"
                class="btn btn-primary"
                href="{{ route('admin.burial.service.print', ['id' => $service->id]) }}"
                role="button"
            >Export PDF</a
            >            
        </div>
    </div>
    
</div>

<script>
    const providerSelect = document.getElementById('burial_service_provider');
    const addressInput = document.getElementById('burial_service_address');
    const contactInput = document.getElementById('burial_service_contact');
    
    providerSelect.onload('change', (e) => {
        const selectedOption = e.target.options[e.target.selectedIndex];
        const address = selectedOption.getAttribute('data-address') || '';
        const barangay = selectedOption.getAttribute('data-barangay') || '';
        const contact = selectedOption.getAttribute('data-contact') || '';
        console.log(address, barangay, contact);

        addressInput.value = address + ", " + barangay;
        contactInput.value = contact;
    });
</script>

@endsection