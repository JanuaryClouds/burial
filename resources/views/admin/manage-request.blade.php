@extends('layouts.admin')
@section('content')
@section('breadcrumb')
<x-breadcrumb :items="[
    ['label' => 'Burial Requests', 'url' => route('admin.burial.requests')],
    ['label' => $serviceRequest->deceased_firstname, 'url' => ''],
    ]"/>
@endsection
<title>{{ $serviceRequest->deceased_firstname }}'s Burial</title>
<div class="flex flex-col gap-8">
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
                    <input type="text" value="{{ $serviceRequest->deceased_lastname }}" readonly name="deceased_lastname" id="deceased_lastname" class="border-2 border-gray-300 rounded-md px-2 py-1">
                    <label for="deceased_lastname" class="text-sm text-gray-400 text-center">Last Name*</label>
                </span>
                <span class="flex flex-col w-2/3 justify-between">
                    <input type="text" readonly value="{{ $serviceRequest->deceased_firstname }}" name="deceased_firstname" id="deceased_firstname" class="border-2 border-gray-300 rounded-md px-2 py-1">
                    <label for="deceased_firstname" class="text-sm text-gray-400 text-center">First Name*</label>
                </span>
            </div>
            <h4 class="text-lg font-semibold">Representative / Contact Person</h4>
            <div class="flex justify-start items-center w-full gap-2">
                <span class="flex flex-col w-1/3 justify-between">
                    <input type="text" value="{{ $serviceRequest->representative }}" name="representative" id="representative" class="border-2 border-gray-300 rounded-md px-2 py-1">
                    <label for="representative" class="text-sm text-gray-400 text-center">Full Name*</label>
                </span>
                <span class="flex flex-col w-1/3 justify-between">
                    <input type="text" readonly value="{{ $serviceRequest->representative_contact }}" name="representative_contact" id="representative_contact" class="border-2 border-gray-300 rounded-md px-2 py-1">
                    <label for="representative_contact" class="text-sm text-gray-400 text-center">Contact Details*</label>
                </span>

                <!-- NOTE: Can be a dropdown menu -->
                <span class="flex flex-col w-1/3 justify-between">
                    <select readonly name="rep_relationship" id="rep_relationship" class="border-2 border-gray-300 rounded-md px-2 py-1">
                        @if ($serviceRequest)
                            <option value="{{ $serviceRequest->rep_relationship }}">{{ $relationships->firstWhere('id', $serviceRequest->rep_relationship)->name ?? 'Unknown' }}</option>
                        @else
                            <option value="">Select Relationship*</option>
                        @endif
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
                        <input type="text" readonly value="{{ $serviceRequest->burial_address }}" name="burial_address" id="burial_address" class="border-2 border-gray-300 rounded-md px-2 py-1">
                        <label for="burial_address" class="text-sm text-gray-400 text-center">Building Number, House No., Street*</label>
                    </span>
                    <span class="flex flex-col w-2/6 justify-between">
                        <select name="barangay_id" readonly id="barangay_id" required class="border-2 border-gray-300 rounded-md px-2 py-1">
                            @if ($serviceRequest)
                                <option value="{{ $serviceRequest->barangay_id }}">{{ $barangays->firstWhere('id', $serviceRequest->barangay_id)->name ?? 'Unknown' }}</option>
                            @else
                                <option value="">Select Barangay*</option>
                            @endif
                        </select>
                        <label for="barangay_id" class="text-sm text-gray-400 text-center">Barangay*</label>
                    </span>
                </div>
                <div class="flex justify-between items-center w-full gap-2">
                    <h5 class="">Date of Burial</h5>
                    <span class="flex justify-between items-center gap-2">
                        <label for="start_of_burial" class="text-sm text-gray-400 text-center">Start*</label>
                        <p class="text-sm text-gray-700 font-semibold {{ $serviceRequest ? '' : 'hidden' }}">{{ $serviceRequest ? Str::limit($serviceRequest->start_of_burial, 10) : '' }}</p>
                    </span>
                    <span class="flex justify-between items-center gap-2">
                        <label for="end_of_burial" class="text-sm text-gray-400 text-center">End*</label>
                        <p class="text-sm text-gray-700 font-semibold {{ $serviceRequest ? '' : 'hidden' }}">{{ $serviceRequest ? Str::limit($serviceRequest->end_of_burial, 10) : '' }}</p>
                    </span>
                </div>
            </div>
        </div>

        <hr class="border-2 border-dashed border-gray-700">
        <div class="flex flex-col gap-2">
            <h4 class="text-lg font-semibold">Death Certificate*</h4>
            @foreach ($requestImages as $image)
                <img src="data:image/jpeg;base64,{{ base64_encode($image['content']) }}" class="rounded-md shadow-md"/>
            @endforeach
        </div>
        <hr class="border-2 border-dashed border-gray-700">
        <div class="flex flex-col gap-4">
            <h4 class="text-lg font-semibold">Remarks</h4>
            <div class="flex justify-between items-start w-full gap-2">
                <span class="flex flex-col w-full justify-between">
                    <textarea rows="4" readonly name="provider_remarks" class="border-2 border-gray-300 rounded-md px-2 py-1">{{ $serviceRequest->remarks }}</textarea>
                </span>
            </div>
        </div>
    </div>

    @if ($serviceRequest->end_of_burial >= now()->format('Y-m-d'))
        <form action="{{ route('admin.burial.request.update', ['uuid' => $serviceRequest->uuid]) }}" method="post" class="flex justify-center gap-2">
            @csrf
            @method('PUT')
            <select name="status" id="status" class="border-2 border-gray-300 rounded-md px-2 py-1">
                <option value="{{ $serviceRequest->status }}">{{ Str::ucfirst($serviceRequest->status) }}</option>
                @foreach (['pending', 'approved', 'rejected'] as $status)
                    @if ($status != $serviceRequest->status)
                        <option value="{{ $status }}">{{ Str::ucfirst($status) }}</option>
                    @endif                
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 text-white bg-gray-700 rounded-md hover:bg-gray-300 hover:text-black transition-colors cursor-pointer">Update Status</button>
        </form>
    @endif
</div>
@endsection
