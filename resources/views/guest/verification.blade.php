@extends('layouts.guest')
@section('content')
<title>Verify Submission</title>
<div class="container d-flex flex-column min-vh-100 mx-auto align-items-center justify-content-center" x-cloak x-tranition"
>
    <div
        class="row flex-column justify-content-center align-items-center g-2 bg-white p-4 rounded shadow mx-auto w-50 w-md-75 w-lg-50"
    >
        <div class="col text-center">
            <h1>
                Verify Request
            </h1>
        </div>
        <div class="col text-center">
            <p>A verification is sent via your email. Paste the code below to verify your request.</p>
        </div>
        <div class="col d-flex flex-column justify-content-center align-items-center w-100">
            <form action="{{ route('burial.request.store', ['uuid' => session('data')['uuid']]) }}" method="post" class="d-flex flex-column gap-3 align-items-center w-100">
                @csrf
                <div class="mb-3 gap-2 w-50 d-flex flex-column align-items-center">
                    <label for="verification_code" class="form-label text-center">Code
                    </label>
                    <input
                        type="text"
                        class="form-control text-center w-100"
                        name="verification_code"
                        id="code"
                        aria-describedby="helpId"
                        placeholder="XXXXXX"
                    />
                    <button
                        type="submit"
                        class="btn btn-primary w-100"
                    >
                        Verify
                    </button>
                </div>
            </form>
            <form action="{{ route('guest.request.back') }}" method="post">
                @csrf
                <!-- TODO: button is not the correct width -->
                <button type="submit" class="btn btn-secondary w-50">
                    Back
                </button>
            </form>
        </div>
    </div>
    
</div>
@endsection