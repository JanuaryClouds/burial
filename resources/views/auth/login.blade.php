@extends('layouts.auth.login')

@section('content')
<div class="container d-flex min-vh-100 min-vw-100 align-items-center justify-center">
    <div
        class="row d-flex flex-column justify-content-center align-items-center g-2 bg-white p-4 rounded shadow mx-auto w-25 w-md-75 w-lg-50"
    >
        <div class="col d-flex flex-column container">
            <img class="mx-auto" style="width: 100px" src="{{ asset('images/CSWDO.webp') }}" alt="">
            <h2 class="text-2xl font-bold mb-2 text-center text-gray-800">CSWDO Burial
                Assistance</h2>
            <p class="mb-4 text-center text-gray-600 text-sm">
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
                    <a href="#" class="text-blue-500 w-100 hover:underline text-center">Forgot Password?</a>
                </div>
            </form>
        </div>
    </div>
    


    <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
    </div>
</div>
@endsection