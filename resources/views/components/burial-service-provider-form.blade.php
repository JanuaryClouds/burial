<form action="{{ route('admin.burial.new.provider.store') }}" method="post" id="burialServiceProviderForm" class="row flex-column w-75 mx-auto gap-4">
    @csrf
    @method('post')

    <div class="container mx-auto p-4 gap-4 bg-white shadow rounded-md p-4">
        <header class="row d-flex">
            <span class="d-flex gap-2">
                <img src="{{ asset('images/CSWDO.webp') }}" alt="" class="mr-2" style="width: 100px;">
                <span class="d-flex flex-column justify-content-center">
                    <h3 class="fw-semibold">Burial Service Provider Form</h3>
                    <p class="text-lg text-gray-600">CSWDO Burial Assistance</p>
                </span>
            </span>
        </header>
        <small class="text-xs">All fields marked by (*) are required</small>
        <hr class="border-1">
        <div class="flex flex-col gap-4">
            <h4 class="text-black">Details of the Provider</h4>
            <div class="d-flex justify-content-between align-items-start gap-2">
                <span class="d-flex flex-column w-75 justify-content-between">
                    <input type="text" required name="name" id="name" class="form-control">
                    <label for="name" class="form-label text-center">Name of Company*</label>
                </span>
                <span class="d-flex flex-column justify-content-between">
                    <input type="text" required name="contact_details" id="contact_details" class="form-control">
                    <label for="contact_details" class="form-label text-center">Contact Details*</label>
                </span>
            </div>
            <div class="d-flex justify-content-between align-items-start w-100 gap-1">
                <span class="d-flex flex-column w-100 justify-content-between">
                    <input type="text" required name="address" id="address" class="form-control">
                    <label for="address" class="form-label text-center">Address(Lot No., Building No., Street)*</label>
                </span>
                <span class="d-flex flex-column w-100 justify-content-between">
                    <select required name="barangay_id" id="barangay_id" class="form-select">
                        <option value="">Select Barangay</option>
                        @foreach ($barangays as $barangay)
                            <option value="{{ $barangay->id }}">{{ $barangay->name }}</option>
                        @endforeach
                    </select>
                    <label for="provider_address_barangay" class="form-label text-center">Barangay*</label>
                </span>
            </div>
        </div>
        <hr class="border-2">
        <div class="d-flex flex-column gap-2">
            <h4 class="fw-semibold">Remarks</h4>
            <div class="d-flex justify-content-between align-items-start w-100 gap-1">
                <span class="d-flex flex-column w-100 justify-content-between">
                    <textarea type="text" rows="4" name="remarks" class="form-control"></textarea>
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