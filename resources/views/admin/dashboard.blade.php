@extends('layouts.stisla.admin')
@section('content')
<title>Dashboard</title>
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Dashboard</h1>
        </div>
    </section>
    <section class="section">
        <div class="section-title">Charts</div>
        <div class="row m-0">
            <div class="col-12 col-lg-6 bg-white shadow-sm px-3 py-5">
                <canvas 
                    id="applicationsDistributions"
                    data-chart-data='@json($perBarangay->pluck('count'))'
                    data-chart-labels='@json($perBarangay->pluck('name'))'
                ></canvas>
            </div>
        </div>
    </section>
</div>

@endsection