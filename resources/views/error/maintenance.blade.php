<!DOCTYPE html>
<html lang="en">

<head>
    @include('partials.landing-head')
</head>

<body>
    @include('components.alert')
    @include('partials.landing-nav')
    <section id="hero" class="hero">
        <div class="container-xxl">
            <div class="center">
                <p class="eyebrow">Funeral Assistance System</p>
                <h1 class="title">Under Maintenance</h1>
                <h2 class="quote">City Social Welfare & Development Office</h2>
                <div class="actions">
                    @auth()
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
                    @endauth
                </div>
            </div>
        </div>
        <button id="scrollArrow" class="hero-arrow" type="button" aria-label="Scroll to top">
            <i class="fa-solid fa-chevron-down" aria-hidden="true"></i>
        </button>
    </section>

    @include('partials.landing-footer')
    @include('partials.landing-js')
</body>

</html>
