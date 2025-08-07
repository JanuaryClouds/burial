@extends('layouts.admin')
@section('content')
@section('breadcrumb')
<x-breadcrumb :items="[
    ['label' => 'Burial Requests', 'url' => route('admin.burial.requests')],
    ['label' => $serviceRequest->deceased_firstname, 'url' => ''],
    ]"/>
@endsection
<title>{{ $serviceRequest->deceased_firstname }}'s Burial</title>
<div class="container d-flex flex-column gap-4">
    <div class="container d-flex flex-column gap-2 bg-white shadow rounded p-4 mx-auto">
        <header class="row d-flex">
            <span class="d-flex align-items-center gap-2">
                <img src="{{ asset('images/CSWDO.webp') }}" alt="" class="mr-2" style="width: 100px">
                <span class="d-flex flex-column justify-content-center gap-2">
                    <h3 class="fw-semibold mb-0">Burial Service Form</h3>
                    <p class="text-lg text-gray-600 mb-0">CSWDO Burial Assistance</p>
                </span>
            </span>
        </header>
        <small class="text-xs">All fields marked by (*) are required</small>
        <hr class="border-1">

        <div class="row row-gap-2">
            <h4 class="text-black">Details of the Deceased</h4>
            <div class="d-flex justify-content-between align-items-start w-100 gap-2">
                <span class="d-flex flex-column w-25 justify-content-between">
                    <input type="text" value="{{ $serviceRequest->deceased_lastname }}" readonly name="deceased_lastname" id="deceased_lastname" class="form-control">
                    <label for="deceased_lastname" class="form-label text-center">Last Name*</label>
                </span>
                <span class="d-flex flex-column w-75 justify-content-between">
                    <input type="text" readonly value="{{ $serviceRequest->deceased_firstname }}" name="deceased_firstname" id="deceased_firstname" class="form-control">
                    <label for="deceased_firstname" class="form-label text-center">First Name*</label>
                </span>
            </div>
            <h4 class="fw-semibold text-black">Representative / Contact Person</h4>
            <div class="d-flex justify-content-start align-items-center w-100 gap-1">
                <span class="d-flex flex-column w-50 justify-content-between">
                    <input type="text" value="{{ $serviceRequest->representative }}" name="representative" id="representative" class="form-control">
                    <label for="representative" class="form-label text-center">Full Name*</label>
                </span>
                <span class="d-flex flex-column w-25 justify-content-between">
                    <input type="text" readonly value="{{ $serviceRequest->representative_contact }}" name="representative_contact" id="representative_contact" class="form-control">
                    <label for="representative_contact" class="form-label text-center">Contact Details*</label>
                </span>

                <span class="d-flex flex-column w-25 justify-content-between">
                    <select readonly name="rep_relationship" id="rep_relationship" class="form-select">
                        @if ($serviceRequest)
                            <option value="{{ $serviceRequest->rep_relationship }}">{{ $relationships->firstWhere('id', $serviceRequest->rep_relationship)->name ?? 'Unknown' }}</option>
                        @else
                            <option value="">Select Relationship*</option>
                        @endif
                    </select>
                    <label for="rep_relationship" class="form-label text-center">Relationship to the Deceased*</label>
                </span>
            </div>
            <hr class="border-1">

            <div class="d-flex flex-column gap-1">
                <h4 class="fw-semibold text-black">Burial Service Details</h4>
                <h5 class="">Address of Burial</h5>
                <div class="d-flex justify-content-between align-items-center w-100 gap-1">
                    <span class="d-flex flex-column w-75 justify-content-between">
                        <input type="text" readonly value="{{ $serviceRequest->burial_address }}" name="burial_address" id="burial_address" class="form-control">
                        <label for="burial_address" class="form-label text-center">Building Number, House No., Street*</label>
                    </span>
                    <span class="d-flex flex-column w-25 justify-content-between">
                        <select name="barangay_id" readonly id="barangay_id" required class="form-select">
                            @if ($serviceRequest)
                                <option value="{{ $serviceRequest->barangay_id }}">{{ $barangays->firstWhere('id', $serviceRequest->barangay_id)->name ?? 'Unknown' }}</option>
                            @else
                                <option value="">Select Barangay*</option>
                            @endif
                        </select>
                        <label for="barangay_id" class="form-label text-center">Barangay*</label>
                    </span>
                </div>
                <div class="d-flex justify-content-between align-items-center w-100 gap-1">
                    <h5 class="">Date of Burial</h5>
                    <span class="d-flex justify-content-between align-items-center gap-1">
                        <label for="start_of_burial" class="form-label text-center">Start*</label>
                        <p class="fw-semibold {{ $serviceRequest ? '' : 'hidden' }}">{{ $serviceRequest ? Str::limit($serviceRequest->start_of_burial, 10) : '' }}</p>
                    </span>
                    <span class="d-flex justify-content-between align-items-center gap-1">
                        <label for="end_of_burial" class="form-label text-center">End*</label>
                        <p class="fw-semibold {{ $serviceRequest ? '' : 'hidden' }}">{{ $serviceRequest ? Str::limit($serviceRequest->end_of_burial, 10) : '' }}</p>
                    </span>
                </div>
            </div>
        </div>

        <hr class="border-1">
        <div class="d-flex flex-column gap-1">
            <h4 class="fw-semibold">Death Certificate*</h4>
            @foreach ($requestImages as $image)
                <img src="data:image/jpeg;base64,{{ base64_encode($image['content']) }}" class="rounded"/>
            @endforeach
        </div>
        <hr class="border-1">
        <div class="d-flex flex-column gap-2">
            <h4 class="fw-semibold">Remarks</h4>
            <div class="d-flex justify-content-between align-items-start w-100 gap-1">
                <span class="d-flex flex-column w-100 justify-content-between">
                    <textarea rows="4" readonly name="provider_remarks" class="form-control">{{ $serviceRequest->remarks }}</textarea>
                </span>
            </div>
        </div>
    </div>

    @if ($serviceRequest->start_of_burial > now()->format('Y-m-d'))
        <form action="{{ route('admin.burial.request.update', ['uuid' => $serviceRequest->uuid]) }}" method="post" class="d-flex justify-content-center gap-1">
            @csrf
            @method('PUT')
            <select name="status" id="status" class="form-control w-25">
                <option value="{{ $serviceRequest->status }}">{{ Str::ucfirst($serviceRequest->status) }}</option>
                @foreach (['pending', 'approved', 'rejected'] as $status)
                    @if ($status != $serviceRequest->status)
                        <option value="{{ $status }}">{{ Str::ucfirst($status) }}</option>
                    @endif                
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary">Update Status</button>
        </form>
    @endif
</div>
@endsection
