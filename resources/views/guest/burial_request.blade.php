@extends('layouts.guest')
@section('content')
                <title>CSWDO Burial Assistance Form</title>
                <a href="/" class="flex items-baseline self-baseline gap-2 mb-6 font-semibold text-gray-700 dark:text-gray-200">
                                <i class="fa-solid fa-arrow-left"></i>
                                Back
                                </a>

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
@endsection