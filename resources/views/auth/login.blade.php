@extends('layouts.metronic.guest')
<title>Login</title>
@section('content')
<section class="section d-flex justify-content-center align-items-center p-5 min-vh-100">
    <div class="section-body d-flex align-items-center">
        <div
            class="row d-flex flex-lg-row flex-column bg-white rounded shadow-sm p-4"
        >
            <div class="col d-flex flex-column align-items-center">
                <img class="" style="width: 100px" src="{{ asset('images/CSWDO.webp') }}" alt="">
                <h2 class="text-lg-start text-center">CSWDO Burial
                    Assistance</h2>
                <p class="text-start text-sm-center">
                    Please enter your credentials to access the dashboard. If you need assistance, contact your
                    administrator.
                </p>
            </div>
            @include('components.alert')
            <div class="col">
                <form action="{{ route('login') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" placeholder="Enter your email"
                            class="form-control"
                            required>
                    </div>
                    <div class="mb-6" x-data="{ show: false }">
                        <label for="password" class="form-label">Password</label>
                        <div class="d-flex">
                            <input 
                                :type="show ? 'text' : 'password'" 
                                id="password" 
                                name="password"
                                placeholder="Enter your password"
                                class="form-control"
                                required>
                            <button type="button" @click="show = !show"
                                class="btn btn-sm ms-1">
                                <i class="fa-solid fa-eye" 
                                x-show="!show" x-cloak
                                ></i>
                                <i class="fa-solid fa-eye-slash" 
                                x-show="show" x-cloak
                                ></i>
                            </button>
                        </div>
                    </div>
                    <div class="mt-4 d-flex">
                        <button type="submit"
                            class="btn btn-primary w-100">Login</button>
                    </div>
                    <div class="mt-3 d-flex justify-content-between text-sm">
                        <a href="/" class="text-blue-500 w-50 hover:underline text-center">Go Back</a>
                        <a href="#" class="text-blue-500 w-50 hover:underline text-center">Forgot Password?</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
@endsection