@extends('layouts.metronic.admin')
<title>{{ $page_title }}</title>
@section('content')
    @include('burial.partials.datatable')
    <x-applications-modal-loader />
@endsection
