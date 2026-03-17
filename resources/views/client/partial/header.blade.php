<div class="landing-header bg-body" data-kt-sticky="true" data-kt-sticky-name="landing-header"
    data-kt-sticky-offset="{default: '200px', lg: '200px'}" style="animation-duration: 0.3s;">
    {{-- begin:wrapper --}}
    <div class="d-flex flex-center w-100 px-6 py-4">
        <div class="container-xxl">
            <div class="row">
                <div class="col d-flex align-items-center gap-2">
                    <img src="{{ asset('images/CSWDO.webp') }}" alt="CSWDO Logo"
                        class="landing-logo w-50px h-50px d-none d-md-block d-lg-block" />
                    <img src="{{ asset('images/city_logo.webp') }}" alt="Taguig City Logo"
                        class="landing-logo w-50px h-50px d-none d-md-block d-lg-block" />
                    <h1 class="ms-4 mb-0 d-none d-md-block d-lg-block">
                        @if (Route::is('landing.page'))
                            {{ session()->has('citizen') ? 'Welcome, ' . session('citizen')['firstname'] : '' }}
                        @else
                            {{ $page_title ?? 'CSWDO Funeral Assistance System' }}
                        @endif
                    </h1>
                </div>

                <div class="col d-flex align-items-center justify-content-end gap-4">
                    @include('components.theme-toggle')
                    @if (!Route::is('general.intake.form'))
                        <a href="{{ url()->previous() }}" class="btn btn-light">
                            Back
                        </a>
                        @auth
                            <div class="dropdown">
                                <button class="btn btn-light dropdown-toggle" type="button" id="menu"
                                    data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    Menu
                                </button>
                                <div class="dropdown-menu" aria-labelledby="menu">
                                    <a class="dropdown-item" href="{{ route('landing.page') }}">Home</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" href="{{ route('general.intake.form') }}">Apply</a>
                                    <a class="dropdown-item" href="{{ route('client.history') }}">History</a>
                                </div>
                            </div>
                        @endauth
                        @auth
                            <form action="{{ route('logout') }}" method="POST" class="d-block mb-0">
                                @csrf
                                <button type="submit" class="btn btn-lg btn-danger">
                                    Logout
                                </button>
                            </form>
                        @endauth
                    @endif
                    @includeWhen(Route::is('general.intake.form'), 'client.partial.create-form-buttons')
                </div>
            </div>
        </div>
    </div>
    {{-- end:wrapper --}}
</div>
