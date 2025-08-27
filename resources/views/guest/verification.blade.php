@extends('layouts.guest')
@section('content')
<title>verification</title>
<div class="container d-flex flex-column min-vh-100 mx-auto align-items-center justify-content-center" x-cloak x-tranition"
>
    <div
        class="row flex-column justify-content-center align-items-center g-2 bg-white p-4 rounded shadow mx-auto w-50 w-md-75 w-lg-50"
    >
        <div class="col">
            <h1>
                Verify Request
            </h1>
        </div>
        <div class="col">
            <p>A verification is sent via your email. Paste the code below to verify your request.</p>
        </div>
        <div class="col">
            <form action="{{ route('burial.request.store', ['uuid' => session('data')['uuid']]) }}" method="post" class="d-flex flex-column gap-3">
                @csrf
                <div class="mb-3">
                    <label for="verification_code" class="form-label">Code
                        </label>
                        <input
                        type="text"
                        class="form-control"
                        name="verification_code"
                    id="code"
                    aria-describedby="helpId"
                    placeholder="XXXXXX"
                    />
                </div>
                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Verify
                </button>
            </form>
        </div>
    </div>
    
</div>
@endsection