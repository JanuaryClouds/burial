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
                <h4>Submission of applications is temporarily disabled</h4>
                <div class="actions">
                    @auth
                        @if (auth()->user()->hasRole('superadmin'))
                            <a href="{{ route('system.index') }}" class="btn btn-lg btn-primary hover-scale">
                                Go to Settings
                            </a>
                        @endif
                    @endauth
                </div>
            </div>
        </div>
    </section>
    @include('partials.landing-footer')
    @include('partials.landing-js')
</body>

</html>
