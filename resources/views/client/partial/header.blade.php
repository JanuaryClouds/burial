<div class="landing-header bg-body" data-kt-sticky="true" data-kt-sticky-name="landing-header"
    data-kt-sticky-offset="{default: '200px', lg: '200px'}" style="animation-duration: 0.3s;">
    {{-- begin:wrapper --}}
    <div class="d-flex flex-center w-100 px-6 py-4">
        <div class="container-xxl">
            <div class="row">
                <div class="col d-flex align-items-center gap-2">
                    <img src="{{ asset('images/CSWDO.webp') }}" alt="CSWDO Logo" class="landing-logo w-50px h-50px" />
                    <img src="{{ asset('images/city_logo.webp') }}" alt="Taguig City Logo"
                        class="landing-logo w-50px h-50px" />
                    <h1 class="ms-4 mb-0">
                        {{ session()->has('citizen') ? 'Welcome, ' . session('citizen')['firstname'] : '' }}
                    </h1>
                </div>

                <div class="col d-flex align-items-center justify-content-end gap-4">
                    @include('components.theme-toggle')
                    @if (Route::is('landing.page'))
                        @if (session('citizen') && session('citizen')['user_id'])
                            <a href="{{ route('general.intake.form') }}" class="btn btn-primary hover-scale">
                                Apply
                            </a>
                            @if ($existingClient)
                                <a href="{{ route('client.history') }}" class="btn btn-light hover-scale">
                                    History
                                </a>
                                <a href="{{ route('landing.page', ['uuid' => 'logout']) }}" class="btn btn-danger">
                                    <i class="fa-solid fa-right-from-bracket pe-0"></i>
                                </a>
                            @endif
                        @else
                            <a href="https://tlcportal.taguig.gov.ph/login" class="btn btn-primary hover-scale">
                                Register
                            </a>
                            @if (Route::is('landing.page'))
                                <a href="{{ route('login.page') }}" class="btn btn-light hover-scale">
                                    Sign In
                                </a>
                            @endif
                        @endif
                    @elseif (!Route::is('general.intake.form'))
                        <a href="{{ url()->previous() }}" class="btn btn-light">
                            Back
                        </a>
                        <div class="dropdown">
                            <button class="btn btn-light dropdown-toggle" type="button" id="menu"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Menu
                            </button>
                            <div class="dropdown-menu" aria-labelledby="menu">
                                <a class="dropdown-item" href="{{ route('landing.page') }}">Home</a>
                                <a class="dropdown-item" href="{{ route('general.intake.form') }}">Apply</a>
                                <a class="dropdown-item" href="{{ route('client.history') }}">History</a>
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('landing.page', ['uuid' => 'logout']) }}"
                                    class="dropdown-item">Logout</a>
                            </div>
                        </div>

                    @endif
                    @includeWhen(Route::is('general.intake.form'), 'client.partial.create-form-buttons')
                </div>
            </div>
        </div>
    </div>
    {{-- end:wrapper --}}
</div>
