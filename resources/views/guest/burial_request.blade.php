@extends('layouts.guest')
@section('content')
   <title>CSWDO Burial Assistance Form</title>
   <div class="container d-flex flex-column justify-content-center align-items-center px-auto m-5">
      @if ($errors->any())
         <div
            class="alert alert-warning"
            role="alert"
         >
            <ul class="list-group list-group-numbered">
               @foreach ($errors->all() as $error)
                  <li>{{ $error }}</li>
               @endforeach
            </ul>
         </div>
      @endif
      <x-burial-assistance-request-form />
   </div>
@endsection