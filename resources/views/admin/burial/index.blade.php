@extends('layouts.metronic.admin')
<title>{{ $page_title }}</title>
@section('content')
@include('admin.burial.partial.datatable')        
<x-applications-modal-loader />

@endsection