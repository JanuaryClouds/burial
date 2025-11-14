@extends('layouts.stisla.admin')
<title>Burial Assistance Applications</title>
@section('content')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Burial Assistance Applications</h1>
            </div>
            <div class="section-header-button">
            </div>
        </section>
        @include('admin.burial.partial.datatable')        
    </div>
<x-applications-modal-loader />

@endsection