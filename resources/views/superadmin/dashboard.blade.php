@extends('layouts.superadmin')
<title>Dashboard</title>
@section('content')
<div class="container d-flex flex-column gap-4 g-0 p-0">
    <h1 class="g-0 bg-white p-3 rounded shadow-sm w-100 mb-0">Dashboard</h1>
    <div class="d-flex justify-content-start gap-4 p-0">
        <div class="card mb-3 rounded shadow-sm" style="max-width: 480px;" >
            <div class="row g-0">
                <div class="col-md-4 d-flex justify-content-center align-items-center rounded" style="background-color: chartreuse;">
                    <i class="fa-solid fa-asterisk" style="font-size: 50px;"></i>
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <h5 class="card-title">{{ $totalData }}</h5>
                        <p class="card-text text-muted">
                            Total number of data including Burial Assistance Requests, Service Providers, and Burials Serviced.
                        </p>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</div>

@endsection