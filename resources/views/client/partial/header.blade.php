<div class="landing-header bg-white" data-kt-sticky="true" data-kt-sticky-name="landing-header"
    data-kt-sticky-offset="{default: '200px', lg: '200px'}" style="animation-duration: 0.3s;">
    {{-- begin:wrapper --}}
    <div class="d-flex flex-center w-100 px-6 py-4">
        <div class="container-xxl">
            <div class="row">
                <div class="col d-flex gap-2 p-2">
                    <img src="{{ asset('images/CSWDO.webp') }}" alt="CSWDO Logo" class="landing-logo w-75px h-75px" />
                    <img src="{{ asset('images/city_logo.webp') }}" alt="Taguig City Logo"
                        class="landing-logo w-75px h-75px" />
                </div>

                <div class="col d-flex align-items-center justify-content-end gap-4">
                    @if (Route::is('landing.page'))
                        @if (env('APP_DEBUG') && session()->has('citizen'))
                            <a href="{{ route('landing.page', ['uuid', 'debug']) }}"
                                class="btn btn-danger fw-bold hover-scale">
                                Clear Session
                            </a>
                        @endif
                        @if (session('citizen') && session('citizen')['user_id'])
                            <a href="{{ route('general.intake.form') }}" class="btn btn-primary fw-bold hover-scale">
                                Apply
                            </a>
                        @else
                            <a href="https://tlcportal.taguig.gov.ph/login" class="btn btn-primary fw-bold hover-scale">
                                Register
                            </a>
                        @endif
                    @endif
                    @includeWhen(Route::is('general.intake.form'), 'client.partial.create-form-buttons')
                </div>
            </div>
        </div>
    </div>
    {{-- end:wrapper --}}
</div>
