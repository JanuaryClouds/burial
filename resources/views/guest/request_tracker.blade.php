@props(['serviceRequest' => null])

@extends('layouts.guest')
@section('content')
    <title>CSWDO Burial Assistance Request Tracker</title>
        <div
            class="row justify-content-start align-items-baseline g-2"
        >
            <a href="/" class="col g-2 text-decoration-none text-secondary flex-shrink-1">
                <i class="fa-solid fa-arrow-left"></i>
                Back
            </a>
            <div
                class="col flex justify-content-end align-items-baseline gap-3"
            >
                <p class="d-none d-md-block">Request Status:</p>
                @if ($serviceRequest->status === 'pending')
                    <span class="badge bg-secondary">Pending</span>
                @elseif ($serviceRequest->status === 'approved')
                    <span class="badge bg-success">Approved</span>
                @elseif ($serviceRequest->status === 'rejected')
                    <span class="badge bg-danger">Rejected</span>
                @endif
            </div>
        </div>
    <div class="container align-items-start justify-content-start">
        
    </div>
@endsection