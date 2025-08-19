@extends('layouts.admin')
@section('content')
@section('breadcrumb')
<x-breadcrumb :items="[
    ['url' => route('admin.burial.providers'), 'label' => 'Burial Service Providers'],
    ['url' => '', 'label' => $serviceProvider->name]
]"/>
@endsection
<title>Update Burial Service Provider</title>
@if (session('error'))
    <x-alert type="error" :message="session('error')" />
@endif
<div class="container d-flex flex-column justify-content-between align-items-center">
    <form action="{{ route('admin.burial.provider.update', ['id' => $serviceProvider->id]) }}" method="POST" id="burialServiceProviderForm" class="row flex-column bg-white align-items-center shadow w-50 p-2 py-4 gap-4">
    @csrf
    @method('PUT')

    <div class="row flex-column w-100 gap-4">
        <header class="row d-flex">
            <span class="d-flex gap-2">
                <img src="{{ asset('images/CSWDO.webp') }}" alt="" class="mr-2" style="width: 100px;">
                <span class="d-flex flex-column justify-content-center">
                    <h3 class="fw-semibold">Burial Service Provider Form</h3>
                    <p class="text-lg text-gray-600">CSWDO Burial Assistance</p>
                </span>
            </span>
        </header>
        <small class="">All fields marked by (*) are required</small>
        <hr class="border-1">
        <div class="d-flex flex-column gap-1">
            <h4 class="text-black tw-semibold">Details of the Provider</h4>
            <div class="d-flex justify-content-between align-items-start w-100 gap-1">
                <span class="d-flex flex-column w-75 justify-content-between">
                    <input type="text" required value="{{ $serviceProvider->name }}" name="name" id="name" class="form-control">
                    <label for="name" class="form-label">Name of Company*</label>
                </span>
                <span class="d-flex flex-column w-25 justify-content-between">
                    <input type="text" readonly value="{{ $serviceProvider->contact_details }}" name="contact_details" id="contact_details" class="form-control">
                    <label for="contact_details" class="form-label">Contact Details*</label>
                </span>
            </div>
            <div class="d-flex justify-content-between align-items-start w-100 gap-1">
                <span class="d-flex flex-column w-100 justify-content-between">
                    <input type="text" required value="{{ $serviceProvider->address }}" name="address" id="address" class="form-control">
                    <label for="address" class="form-label">Address(Lot No., Building No., Street)*</label>
                </span>
                <span class="d-flex flex-column w-100 justify-content-between">
                    <select required value="{{ $serviceProvider->barangay_id }}" name="barangay_id" id="barangay_id" class="form-select">
                        <option value="{{ $serviceProvider->barangay_id }}">{{ $serviceProvider->barangay->name }}</option>
                        @foreach ($barangays as $barangay)
                            <option value="{{ $barangay->id }}">{{ $barangay->name }}</option>
                        @endforeach
                    </select>
                    <label for="provider_address_barangay" class="form-label">Barangay*</label>
                </span>
            </div>
        </div>
        <hr class="border-1">
        <div class="d-flex flex-column gap-2">
            <h4 class="fw-semibold">Remarks</h4>
            <div class="d-flex justify-content-between align-items-start w-100 gap-1">
                <span class="d-flex flex-column w-100 justify-content-between">
                    <textarea type="text" rows="4" name="remarks" class="form-control">{{ $serviceProvider->remarks }}</textarea>
                </span>
            </div>
        </div>
    </div>
    <span class="d-flex justify-content-center align-items-center gap-2">
        <button type="submit" class="btn btn-primary">
            Update
        </button>
        <a href="{{ route('admin.burial.providers') }}" class="btn btn-secondary">
            Cancel
        </a>
    </span>
</form>
</div>

@endsection