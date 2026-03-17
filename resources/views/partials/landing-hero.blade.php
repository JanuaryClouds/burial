<section id="hero" class="hero">
    <div class="container-xxl">
        <div class="center">
            <p class="eyebrow">City Government of Taguig</p>
            <h1 class="title">Funeral Assistance System</h1>
            <h2 class="quote">City Social Welfare & Development Office</h2>
            <div class="actions">
                @auth()
                    <a href="{{ route('general.intake.form') }}" class="btn btn-primary btn-lg hover-scale">
                        Apply
                    </a>
                    @if (auth()->user()->clients()->count() > 0)
                        <a href="{{ route('client.history') }}" class="btn btn-lg btn-light hover-scale">
                            History
                        </a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST" class="block mb-0">
                        @csrf
                        <button type="submit" class="btn btn-lg btn-danger">
                            Logout
                        </button>
                    </form>
                @else
                    <a href="{{ config('services.portal.url') }}" class="btn btn-lg btn-primary hover-scale">
                        Register
                    </a>
                @endauth
                <button type="button" class="btn btn-secondary btn-lg" data-bs-toggle="modal"
                    data-bs-target="#trackAssistanceModal">
                    Track Assistance
                </button>
            </div>
        </div>
    </div>
    <button id="scrollArrow" class="hero-arrow" type="button" aria-label="Scroll to LANI section">
        <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
    </button>
</section>
