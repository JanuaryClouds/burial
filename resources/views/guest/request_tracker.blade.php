@props(['serviceRequest' => null])

@extends('layouts.guest')
@section('content')
    <title>CSWDO Burial Assistance Request Tracker</title>
    <div
        class="row fixed-top z-1 justify-content-center align-items-center p-4 bg-white bg-opacity-75 shadow"
    >
        <div class="col">
            <a href="/" class="text-decoration-none text-primary">
                <i class="fa-solid fa-arrow-left"></i>
                Back
            </a>
        </div>
        <div class="col d-flex justify-content-end align-items-center gap-2">
            <p class="d-none d-md-inline align-middle mb-0">Request Status:</p>
            @if ($serviceRequest->status === 'pending')
                <span class="badge bg-primary h-50">Pending</span>
            @elseif ($serviceRequest->status === 'approved')
                <span class="badge bg-success">Approved</span>
            @elseif ($serviceRequest->status === 'rejected')
                <span class="badge bg-danger">Rejected</span>
            @endif
        </div>
    </div>
    <div class="container d-flex justify-content-center align-middle p-5 mt-5">
        <x-burial-assistance-request-form :serviceRequest="$serviceRequest" />
    </div>
@endsection