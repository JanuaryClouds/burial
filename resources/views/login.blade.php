@extends('layouts.metronic.guest')
<title>Login</title>
@section('content')
    <section class="section d-flex justify-content-center align-items-center min-vh-100">
        <!-- begin::Aside -->
        <div
            class="d-flex flex-root flex-lg-column flex-lg-row-fluid flex-lg-column-fluid flex-lg-grow-auto flex-lg-center w-xl-25 gap-lg-10 p-2 p-lg-0">
            <!-- begin::header -->
            <div class="d-flex flex-center">
                <a href="{{ route('landing.page') }}">
                    <img src="{{ asset('images/CSWDO.webp') }}" alt="" class="h-lg-250px h-20px">
                </a>
            </div>
            <!-- end::header -->
            <!-- begin::Body -->
            <div class="d-flex flex-row flex-lg-column flex-center gap-1">
                <h1 class=" mb-9 fs-2hx d-none d-lg-block text-uppercase">City Social Welfare & Development Office</h1>
                <h2 class=" mb-0 fs-2hx">Funeral Assistance System</h2>
            </div>
            <!-- end::Body -->
        </div>
        <!-- end::Aside -->
        <div class="bg-body d-flex flex-center flex-column align-items-stretch w-xl-500px h-100 w-200 rounded shadow-sm  ">

            <div class="d-flex flex-column-fluid flex-lg-row-auto justify-content-center justify-content-lg-end p-12">
                <div class="d-flex flex-center flex-column flex-column-fluid pb-15 pb-lg-20">
                    <h1 class="text-dark fw-bolder mb-3">Sign In</h1>
                    <div class="col d-flex flex-column flex-center">
                        <p class="text-start text-sm-center text-muted fw-semibold fs-6">
                            Please enter your credentials to access the dashboard.
                        </p>
                    </div>
                    @include('components.alert')
                    <div class="col ">
                        <form action="{{ route('login.check') }}" method="POST">
                            @csrf
                            <div class="mb-4">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email" placeholder="Enter your email"
                                    class="form-control" required>
                            </div>
                            <div class="mb-6">
                                <label for="password" class="form-label">Password</label>
                                <div class="d-flex">
                                    <input type="password" id="password" name="password" placeholder="Enter your password"
                                        class="form-control" required>
                                    <button type="button" id="togglePassword" class="btn btn-sm ms-1">
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
                                <a href="/" class="text-blue-500 w-50 hover:underline text-center">Go Back</a>
                                <a href="#" class="text-blue-500 w-50 hover:underline text-center">Forgot
                                    Password?</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            passwordInput.type = passwordInput.type === 'password' ? 'text' : 'password';
        })
    </script>
@endsection
