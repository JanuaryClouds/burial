@extends('layouts.app')
@section('content')
    <x-record-export report="applications" />
	<livewire:application.index />
@endsection
