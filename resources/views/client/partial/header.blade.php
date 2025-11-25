<div class="landing-header" data-kt-sticky="true" data-kt-sticky-name="landing-header"
    data-kt-sticky-offset="{default: '200px', lg: '300px'}" style="animation-duration: 0.3s;">
    {{-- begin:wrapper --}}
    <div class="d-flex flex-center w-100 px-6 py-4">
        <div class="container-xxl">
            <div class="row">
                <div class="col d-flex gap-2">
                    <img src="{{ asset('images/CSWDO.webp') }}" alt="CSWDO Logo" class="landing-logo w-75px h-75px" />
                    <img src="{{ asset('images/city_logo.webp') }}" alt="Taguig City Logo"
                        class="landing-logo w-75px h-75px" />
                </div>

                <div class="col d-flex flex-column flex-lg-center justify-content-center gap-1">
                    <h1 class="text-start text-nowrap text-uppercase">
                        {{ $header_title ?? 'Funeral Assistance System' }}
                    </h1>
                    <p class="text-start mb-0">
                        {{ $header_subtitle ?? 'City Social Welfare & Development Office' }}
                    </p>
                </div>
                <div class="col d-flex align-items-center justify-content-end gap-4">
                    <a href="{{ route('general.intake.form') }}" class="btn btn-primary fw-bold hover-scale"> Apply </a>
                </div>
            </div>
        </div>
    </div>
    {{-- end:wrapper --}}
</div>
