<!DOCTYPE html>
<html lang="en">
@include('partials.landing-head')

<body>
    @include('components.alert')
    @include('partials.landing-nav')
    @include('partials.landing-hero')
    <div class="py-10" style="background-color: #f4f6f9;">
        <div class="d-flex flex-column" id="start">
            @include('guest.partials.services')
            @include('guest.partials.clients')
            @include('guest.partials.documents')
            @include('guest.partials.process')
        </div>
    </div>
    @include('tracker.partials.form-modal')
    @include('partials.landing-footer')
    <button id="toTop" class="btn" aria-label="Back to top"><i class="fa-solid fa-arrow-up"></i></button>
    @include('partials.landing-js')
</body>

</html>
