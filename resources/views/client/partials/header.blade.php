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
                    <h1 class="ms-4 mb-0 d-none d-md-block">
                        @if (Route::is('landing.page'))
                            {{ session()->has('citizen') ? 'Welcome, ' . (session('citizen')['firstname'] ?? '') : '' }}
                        @else
                            {{ $page_title ?? 'CSWDO Funeral Assistance System' }}
                        @endif
                    </h1>
                </div>

                <div class="col d-flex align-items-center justify-content-end gap-4">
                    @include('components.theme-toggle')
                    @auth
                        <a name="" id="" class="btn btn-primary" href="{{ route('dashboard') }}"
                            role="button">Dashboard</a>
                        <form action="{{ route('logout') }}" method="POST" class="d-block mb-0">
                            @csrf
                            <button type="submit" class="btn btn-lg btn-danger">
                                Logout
                            </button>
                        </form>
                    @endauth
                </div>
            </div>
        </div>
    </div>
    {{-- end:wrapper --}}
</div>
