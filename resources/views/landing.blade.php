@extends('layouts.metronic.guest')
@section('content')
    <div class="d-flex flex-column flex-center min-vh-100" style="margin-top: -10rem; z-index: -1;">
        <p class="eyebrow">Taguig City</p>
        <h1 class="fas-title">Funeral Assistance System</h1>
        <h2 class="fas-quote">City Social Welfare & Development Office</h2>
    </div>

    <div class="bg-body py-10">
        <div class="container-xxl gap-20">
            <div class="d-flex flex-column gap-20">
                <div class="divider">
                    <span class="r"></span>
                    <span class="b"></span>
                    <span class="y"></span>
                </div>
                @include('guest.partial.services')
                <div class="card shadow-sm bg-body multicolor-border">
                    <div class="card-header">
                        <h4 class="card-title fw-bold">Who may avail of service</h4>
                    </div>
                    <div class="card-body">
                        <p class="fs-4">
                            May be availed by immediate family members, relatives or guardians of the deceased provided the
                            deceased
                            is:
                        </p>
                        <ul class="fs-5">
                            <li>An indigent as certified by the City Social Welfare and Development Office</li>
                            <li>A bonafide resident of the City of Taguig for at least five (5) years as determined by the
                                City
                                Social Welfare and Development Office</li>
                            <li>Not a beneficiary of veterans benefits under Ordinance No. 56 series of 2014</li>
                        </ul>
                    </div>
                </div>
                <div class="separator"></div>
                @include('guest.partial.documents')
                <div class="separator"></div>
                @include('guest.partial.process')
                <div class="separator"></div>
                @include('admin.partial.cards')
            </div>
        </div>
    </div>
@endsection
