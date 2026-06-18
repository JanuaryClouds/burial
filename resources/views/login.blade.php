@extends('layouts.guest')
<title>Login</title>
@section('content')
    <div class="container-xxl d-flex align-items-center justify-content-center min-vh-100">
        <div class="card shadow-sm w-100 w-md-75 w-lg-50">
            <div class="card-body d-flex flex-center flex-column">
                <div class="d-flex flex-center flex-column flex-column-fluid">
                    <img src="{{ asset('images/CSWDO.webp') }}" alt="cswdo logo" class="w-25">
                    <h1 class="text-dark fw-bolder mb-3">Sign In</h1>
                    <div class="col d-flex flex-column flex-center">
                        <p class="text-start text-sm-center text-muted fw-semibold fs-6">
                            Please enter your credentials to access the dashboard.
                        </p>
                    </div>
                    @include('components.alert')
                    <div class="col">
                        <form action="{{ route('login.check') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email" placeholder="Enter your email"
                                    class="form-control" required>
                            </div>
                            <div class="mb-6">
                                <label for="password" class="form-label">Password</label>
                                <div class="position-relative">
                                    <input type="password" id="password" name="password" placeholder="Enter your password"
                                        class="form-control pe-10" required>
                                    <button type="button" id="togglePassword"
                                        class="btn btn-sm position-absolute top-50 end-0 translate-middle-y border-0 bg-transparent">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="mt-4 d-flex">
                                <button type="submit" class="btn btn-primary w-100">Login
                                </button>
                            </div>
                            <div class="mt-6 text-sm-center  ">
                                <p class="text-muted"> If you need assistance, contact your administrator</p>
                            </div>
                            <div class="p-1" style="opacity: 0.4;">
                                <hr>
                            </div>
                            <div class="mt-3 d-flex justify-content-between text-sm">
                                <a href="{{ route('landing.page') }}"
                                    class="text-primary text-decoration-underline w-100 text-center">Go
                                    Back</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script nonce="{{ $nonce ?? '' }}">
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            passwordInput.type = passwordInput.type === 'password' ? 'text' : 'password';
            icon.classList.toggle('fa-eye', passwordInput.type === 'password');
            icon.classList.toggle('fa-eye-slash', passwordInput.type === 'text');
        })
    </script>
@endsection
