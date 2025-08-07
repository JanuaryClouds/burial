@extends('layouts.guest')
@section('content')
                <title>CSWDO Burial Assistance Form</title>

                                <!-- <a href="/" class="d-flex justify-content-between align-items-center fixed-top text-decoration-none btn btn-secondary m-4" style="width: 6em;">
                                                <i class="fa-solid fa-arrow-left"></i>
                                                Back
                                </a> -->
                                
                                <div class="container d-flex justify-content-center align-middle p-5">
                                                @if ($errors->any())
                                                <div class="text-red-600 text-sm mb-4">
                                                                <ul class="list-disc pl-5">
                                                                                @foreach ($errors->all() as $error)
                                                                                <li>{{ $error }}</li>
                                                                                @endforeach
                                                                </ul>
                                                </div>
                                                @endif
                                                <x-burial-assistance-request-form />
                                </div>
@endsection