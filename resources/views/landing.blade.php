<!DOCTYPE html>
<html lang="en">
@include('partials.landing-head')

<body>
    @include('partials.landing-nav')
    <section id="fas-hero" class="fas-hero">
        <div class="container">
            <div class="fas-center">
                <p class="fas-eyebrow">City Government of Taguig</p>
                <h1 class="fas-title">Funeral Assistance System</h1>
                <h2 class="fas-quote">City Social Welfare & Development Office</h2>
                <div class="fas-actions">
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
        <button id="scrollArrow" class="fas-hero-arrow" type="button" aria-label="Scroll to LANI section">
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
    <button id="toTop" class="btn" aria-label="Back to top"><i class="fa-solid fa-arrow-up"></i></button>
    @include('partials.landing-js')
</body>

</html>
