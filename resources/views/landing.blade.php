<!DOCTYPE html>
<html lang="en">
@include('partials.landing-head')

<body>
    @include('partials.landing-nav')
    <section id="hero" class="hero">
        <div class="container-xxl">
            <div class="center">
                <p class="eyebrow">City Government of Taguig</p>
                <h1 class="title">Funeral Assistance System</h1>
                <h2 class="quote">City Social Welfare & Development Office</h2>
                <div class="actions">
                    @if (session('citizen') && session('citizen')['user_id'])
                        <a href="{{ route('general.intake.form') }}" class="btn btn-primary btn-lg hover-scale">
                            Apply
                        </a>
                        @if ($existingClient)
                            <a href="{{ route('client.history') }}" class="btn btn-lg btn-light hover-scale">
                                History
                            </a>
                        @endif
                        <a href="{{ route('landing.page', ['uuid' => 'logout']) }}" class="btn btn-lg btn-danger">
                            Logout
                        </a>
                    @else
                        <a href="https://development-eservices.taguig.info/" class="btn btn-lg btn-primary hover-scale">
                            Register
                        </a>
                        @if (session('info') && session('info') == 'Unable to fetch citizen details.')
                            <a href="{{ route('general.intake.form') }}" class="btn btn-lg btn-light hover-scale">
                                Apply without Citizen ID
                            </a>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        <button id="scrollArrow" class="hero-arrow" type="button" aria-label="Scroll to LANI section">
            <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
        </button>
    </section>

    <div class="py-10" style="background-color: #f4f6f9;">
        <div class="d-flex flex-column" id="start">
            @include('guest.partial.services')
            @include('guest.partial.clients')
            @include('guest.partial.documents')
            @include('guest.partial.process')
        </div>
    </div>
    @include('partials.landing-footer')
    <button id="toTop" class="btn" aria-label="Back to top"><i class="fa-solid fa-arrow-up"></i></button>
    @include('partials.landing-js')
</body>

</html>
