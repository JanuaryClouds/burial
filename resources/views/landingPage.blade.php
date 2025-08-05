@extends('layouts.guest')
@section('content')
<title>CSWDO Burial Assistance</title>
<div 
    class="container d-flex min-vh-100 min-vw-100 align-items-center justify-content-center"
>
    <div
        class="row flex-column justify-content-center align-items-center g-2 bg-white p-4 rounded shadow mx-auto w-50 w-md-75 w-lg-50"
    >
        <div class="col d-flex flex-column container">
            <img class="w-25 mx-auto" src="{{ asset('images/CSWDO.webp') }}" alt="" >
            <h3 class="text-center fw-semibold">Welcome to CSWDO Burial Assistance</h3>
        </div>
        <div class="col d-flex flex-column gap-2">
            <a
                name=""
                id=""
                class="btn btn-primary w-50 mx-auto"
                href="{{ route('guest.burial.request') }}"
                role="button"
                >Request Burial Assistance</a
            >
            <p class="text-sm text-center text-gray-600">or use the tracker below if you have requested a burial assistance before</p>
            <div
                class="row d-flex flex-column justify-content-center align-items-center g-2"
            >
                <form action="{{ route('guest.request.tracker', 'uuid') }}" method="post" class="col d-flex flex-column justify-content-center align-items-center">
                    @csrf
                    <input
                        type="text"
                        class="form-control w-50 mx-auto"
                        name="uuid"
                        id="uuid"
                        aria-describedby="helpId"
                        placeholder=""
                    />
                    <button
                        type="submit"
                        class="btn btn-secondary w-50 mx-auto mt-2"
                    >
                        Track Request
                    </button>
                    @if ($errors->any())
                        <div class="alert alert-danger w-75 mx-auto">
                            {{ $errors->first('uuid') }}
                        </div>
                    @endif
                </form>
            </div>
        </div>
        <div class="col d-flex flex-column mt-4">
            <p class="text-sm text-center text-gray-600">For BAO Employees, access the manager below:</p>
            <a
                name=""
                id=""
                class="btn btn-outline-secondary w-50 mx-auto"
                href="{{ route('login.page') }}"
                role="button"
                >Manage Burial Services</a
            >
        </div>
    </div>
</div>
@endsection