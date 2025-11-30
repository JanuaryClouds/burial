<div class="landing-header bg-white" data-kt-sticky="true" data-kt-sticky-name="landing-header"
    data-kt-sticky-offset="{default: '200px', lg: '200px'}" style="animation-duration: 0.3s;">
    {{-- begin:wrapper --}}
    <div class="d-flex flex-center w-100 px-6 py-4">
        <div class="container-xxl">
            <div class="row">
                <div class="col d-flex align-items-center gap-2">
                    <img src="{{ asset('images/CSWDO.webp') }}" alt="CSWDO Logo" class="landing-logo w-50px h-50px" />
                    <img src="{{ asset('images/city_logo.webp') }}" alt="Taguig City Logo"
                        class="landing-logo w-50px h-50px" />
                    <h1 class="fw-semibold ms-4 mb-0">
                        {{ session()->has('citizen') ? 'Welcome, ' . session('citizen')['firstname'] : '' }}
                    </h1>
                </div>

                <div class="col d-flex align-items-center justify-content-end gap-4">
                    @if (Route::is('landing.page'))
                        @if (env('APP_DEBUG') && session()->has('citizen'))
                            <a href="{{ route('landing.page', ['uuid' => 'debug']) }}"
                                class="btn btn-danger  hover-scale">
                                Clear Session
                            </a>
                        @endif
                        @if (session('citizen') && session('citizen')['user_id'])
                            <a href="{{ route('general.intake.form') }}" class="btn btn-primary  hover-scale">
                                Apply
                            </a>
                            @if ($existingClient)
                                <a href="{{ route('client.history') }}" class="btn btn-light hover-scale">
                                    History
                                </a>
                            @endif
                        @else
                            <a href="https://tlcportal.taguig.gov.ph/login" class="btn btn-primary  hover-scale">
                                Register
                            </a>
                            @if (Route::is('landing.page'))
                                <a href="{{ route('login.page') }}" class="btn btn-light  hover-scale">
                                    Sign In
                                </a>
                            @endif
                        @endif
                    @elseif (!Route::is('general.intake.form'))
                        <a href="{{ route('landing.page') }}" class="btn btn-light">
                            Back
                        </a>
                    @endif
                    @includeWhen(Route::is('general.intake.form'), 'client.partial.create-form-buttons')
                </div>
            </div>
        </div>
    </div>
    {{-- end:wrapper --}}
</div>
