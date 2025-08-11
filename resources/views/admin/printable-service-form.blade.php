<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $service->deceased_firstname }} {{ $service->deceased_lastname }}</title>

    <style>
        .min-vw-100 {
            min-width: 100vw !important;
        }

        .row {
            --bs-gutter-x: 1.5rem;
            --bs-gutter-y: 0;
            display: flex;
            flex-wrap: wrap;
            margin-top: calc(-1 * var(--bs-gutter-y));
            margin-right: calc(-0.5 * var(--bs-gutter-x));
            margin-left: calc(-0.5 * var(--bs-gutter-x));
        }
        .row > * {
            flex-shrink: 0;
            width: 100%;
            max-width: 100%;
            padding-right: calc(var(--bs-gutter-x) * 0.5);
            padding-left: calc(var(--bs-gutter-x) * 0.5);
            margin-top: var(--bs-gutter-y);
        }

        .col {
            flex: 1 0 0;
        }

        .row-gap-0 {
            row-gap: 0;
        }

        .vw-100 {
            width: 100vw !important; 
        }

        .g-0,
        .gx-0 {
        --bs-gutter-x: 0;
        }

        .g-0,
        .gy-0 {
        --bs-gutter-y: 0;
        }

        .g-1,
        .gx-1 {
        --bs-gutter-x: 0.25rem;
        }

        .g-1,
        .gy-1 {
        --bs-gutter-y: 0.25rem;
        }

        .g-2,
        .gx-2 {
        --bs-gutter-x: 0.5rem;
        }

        .g-2,
        .gy-2 {
        --bs-gutter-y: 0.5rem;
        }

        .g-3,
        .gx-3 {
        --bs-gutter-x: 1rem;
        }

        .g-3,
        .gy-3 {
        --bs-gutter-y: 1rem;
        }

        .g-4,
        .gx-4 {
        --bs-gutter-x: 1.5rem;
        }

        .g-4,
        .gy-4 {
        --bs-gutter-y: 1.5rem;
        }

        .g-5,
        .gx-5 {
        --bs-gutter-x: 3rem;
        }

        .g-5,
        .gy-5 {
        --bs-gutter-y: 3rem;
        }

        .w-25 {
        width: 25% !important;
        }

        .w-50 {
        width: 50% !important;
        }

        .w-75 {
        width: 75% !important;
        }

        .w-100 {
        width: 100% !important;
        }

        .m-0 {
        margin: 0 !important;
        }

        .m-1 {
        margin: 0.25rem !important;
        }

        .m-2 {
        margin: 0.5rem !important;
        }

        .m-3 {
        margin: 1rem !important;
        }

        .m-4 {
        margin: 1.5rem !important;
        }

        .m-5 {
        margin: 3rem !important;
        }

        .m-auto {
        margin: auto !important;
        }

        .mx-0 {
        margin-right: 0 !important;
        margin-left: 0 !important;
        }

        .mx-1 {
        margin-right: 0.25rem !important;
        margin-left: 0.25rem !important;
        }

        .mx-2 {
        margin-right: 0.5rem !important;
        margin-left: 0.5rem !important;
        }

        .mx-3 {
        margin-right: 1rem !important;
        margin-left: 1rem !important;
        }

        .mx-4 {
        margin-right: 1.5rem !important;
        margin-left: 1.5rem !important;
        }

        .mx-5 {
        margin-right: 3rem !important;
        margin-left: 3rem !important;
        }

        .mx-auto {
        margin-right: auto !important;
        margin-left: auto !important;
        }

        .my-0 {
        margin-top: 0 !important;
        margin-bottom: 0 !important;
        }

        .my-1 {
        margin-top: 0.25rem !important;
        margin-bottom: 0.25rem !important;
        }

        .my-2 {
        margin-top: 0.5rem !important;
        margin-bottom: 0.5rem !important;
        }

        .my-3 {
        margin-top: 1rem !important;
        margin-bottom: 1rem !important;
        }

        .my-4 {
        margin-top: 1.5rem !important;
        margin-bottom: 1.5rem !important;
        }

        .my-5 {
        margin-top: 3rem !important;
        margin-bottom: 3rem !important;
        }

        .my-auto {
        margin-top: auto !important;
        margin-bottom: auto !important;
        }

        .mt-0 {
        margin-top: 0 !important;
        }

        .mt-1 {
        margin-top: 0.25rem !important;
        }

        .mt-2 {
        margin-top: 0.5rem !important;
        }

        .mt-3 {
        margin-top: 1rem !important;
        }

        .mt-4 {
        margin-top: 1.5rem !important;
        }

        .mt-5 {
        margin-top: 3rem !important;
        }

        .mt-auto {
        margin-top: auto !important;
        }

        .me-0 {
        margin-right: 0 !important;
        }

        .me-1 {
        margin-right: 0.25rem !important;
        }

        .me-2 {
        margin-right: 0.5rem !important;
        }

        .me-3 {
        margin-right: 1rem !important;
        }

        .me-4 {
        margin-right: 1.5rem !important;
        }

        .me-5 {
        margin-right: 3rem !important;
        }

        .me-auto {
        margin-right: auto !important;
        }

        .mb-0 {
        margin-bottom: 0 !important;
        }

        .mb-1 {
        margin-bottom: 0.25rem !important;
        }

        .mb-2 {
        margin-bottom: 0.5rem !important;
        }

        .mb-3 {
        margin-bottom: 1rem !important;
        }

        .mb-4 {
        margin-bottom: 1.5rem !important;
        }

        .mb-5 {
        margin-bottom: 3rem !important;
        }

        .mb-auto {
        margin-bottom: auto !important;
        }

        .ms-0 {
        margin-left: 0 !important;
        }

        .ms-1 {
        margin-left: 0.25rem !important;
        }

        .ms-2 {
        margin-left: 0.5rem !important;
        }

        .ms-3 {
        margin-left: 1rem !important;
        }

        .ms-4 {
        margin-left: 1.5rem !important;
        }

        .ms-5 {
        margin-left: 3rem !important;
        }

        .ms-auto {
        margin-left: auto !important;
        }

        .p-0 {
        padding: 0 !important;
        }

        .p-1 {
        padding: 0.25rem !important;
        }

        .p-2 {
        padding: 0.5rem !important;
        }

        .p-3 {
        padding: 1rem !important;
        }

        .p-4 {
        padding: 1.5rem !important;
        }

        .p-5 {
        padding: 3rem !important;
        }

        .px-0 {
        padding-right: 0 !important;
        padding-left: 0 !important;
        }

        .px-1 {
        padding-right: 0.25rem !important;
        padding-left: 0.25rem !important;
        }

        .px-2 {
        padding-right: 0.5rem !important;
        padding-left: 0.5rem !important;
        }

        .px-3 {
        padding-right: 1rem !important;
        padding-left: 1rem !important;
        }

        .px-4 {
        padding-right: 1.5rem !important;
        padding-left: 1.5rem !important;
        }

        .px-5 {
        padding-right: 3rem !important;
        padding-left: 3rem !important;
        }

        .py-0 {
        padding-top: 0 !important;
        padding-bottom: 0 !important;
        }

        .py-1 {
        padding-top: 0.25rem !important;
        padding-bottom: 0.25rem !important;
        }

        .py-2 {
        padding-top: 0.5rem !important;
        padding-bottom: 0.5rem !important;
        }

        .py-3 {
        padding-top: 1rem !important;
        padding-bottom: 1rem !important;
        }

        .py-4 {
        padding-top: 1.5rem !important;
        padding-bottom: 1.5rem !important;
        }

        .py-5 {
        padding-top: 3rem !important;
        padding-bottom: 3rem !important;
        }

        .pt-0 {
        padding-top: 0 !important;
        }

        .pt-1 {
        padding-top: 0.25rem !important;
        }

        .pt-2 {
        padding-top: 0.5rem !important;
        }

        .pt-3 {
        padding-top: 1rem !important;
        }

        .pt-4 {
        padding-top: 1.5rem !important;
        }

        .pt-5 {
        padding-top: 3rem !important;
        }

        .pe-0 {
        padding-right: 0 !important;
        }

        .pe-1 {
        padding-right: 0.25rem !important;
        }

        .pe-2 {
        padding-right: 0.5rem !important;
        }

        .pe-3 {
        padding-right: 1rem !important;
        }

        .pe-4 {
        padding-right: 1.5rem !important;
        }

        .pe-5 {
        padding-right: 3rem !important;
        }

        .pb-0 {
        padding-bottom: 0 !important;
        }

        .pb-1 {
        padding-bottom: 0.25rem !important;
        }

        .pb-2 {
        padding-bottom: 0.5rem !important;
        }

        .pb-3 {
        padding-bottom: 1rem !important;
        }

        .pb-4 {
        padding-bottom: 1.5rem !important;
        }

        .pb-5 {
        padding-bottom: 3rem !important;
        }

        .ps-0 {
        padding-left: 0 !important;
        }

        .ps-1 {
        padding-left: 0.25rem !important;
        }

        .ps-2 {
        padding-left: 0.5rem !important;
        }

        .ps-3 {
        padding-left: 1rem !important;
        }

        .ps-4 {
        padding-left: 1.5rem !important;
        }

        .ps-5 {
        padding-left: 3rem !important;
        }
    </style>

    <!-- @vite('resources/css/app.css')
    @vite('resources/js/app.js') -->
</head>
<body class="min-vh-100 row row-gap-0 vw-100 g-0">
    <form action="#" method="post" enctype="multipart/form-data" id="burialServiceForm" class="row flex-column w-75 mx-auto gap-4">
    @csrf
    @method('post')

    <div class="container mx-auto p-4 gap-4 bg-white shadow rounded-md p-4">
        <header class="row d-flex">
            <span class="d-flex align-items-center gap-2">
                <img src="./images/CSWDO.webp" alt="" class="mr-2" style="width: 100px">
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
                    <label for="burial_service_contact" class="form" text-center">Contact Details</label>
                </span>
            </div>
        </div>

        <hr class="border-2">
        <div class="d-flex flex-column gap-1">
            <h4 class="fw-semibold">Images*</h4>
            @foreach ($serviceImages as $image)
                <!-- <img src="./storage/{{$image}}" class="rounded" style:"">             -->
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js" integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q" crossorigin="anonymous"></script>
</body>
</html>