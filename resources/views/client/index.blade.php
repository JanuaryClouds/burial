@extends('layouts.app')
@section('content')
    <x-record-export report="clients" />
	<livewire:client.index />
@endsection
