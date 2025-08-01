@extends('layouts.guest')
@section('content')
<title>CSWDO Burial Assistance</title>
<div class="d-flex min-vh-100 align-items-center justify-content-center">
    <div class="container align-items-center justify-content-center bg-white p-4 rounded shadow mx-auto w-100 w-md-75 w-lg-50" style="max-width: 600px;">
        <div class="container">
            <img class="w-30 mx-auto block" src="{{ asset('images/CSWDO.webp') }}" alt="">
            <h3 class="text-center fw-semibold">Welcome to CSWDO Burial Assistance</h3>
        </div>
        <div class="container flex flex-column gap-4">
            <p class="text-center text-sm text-gray-600">Please choose your next action</p>
            <div class="flex flex-col gap-4 w-full">
                <a
                    name=""
                    id=""
                    class="btn btn-primary w-50 mx-auto"
                    href="{{ route('guest.burial.request') }}"
                    role="button"
                    >Request Burial Assistance</a
                >
                <form action="{{ route('guest.request.tracker', 'uuid') }}" method="post" class="container flex flex-column align-items-center">
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
                
                <span class="flex flex-col gap-2 items-center">
                    <a
                        name=""
                        id=""
                        class="btn btn-outline-secondary w-50 mx-auto"
                        href="{{ route('login.page') }}"
                        role="button"
                        >Manage Burial Services</a
                    >
                    <p class="text-sm text-center text-gray-600">This action is only intended for CSWDO Employees and <br> BAO employees of the Taguig City Hall</p>
                </span>
                </div>
            </div>
    </div>
</div>

@endsection