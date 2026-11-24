@extends('layouts.app')
@section('content')
    <x-record-export report="beneficiaries" />
	<livewire:beneficiary.index />
@endsection
