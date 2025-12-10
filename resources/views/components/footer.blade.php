<div class="row min-h-150px" style="background-color: #071437">
    <div class="col d-flex justify-content-center align-items-center gap-4 ps-10 text-white">
        <img src="{{ asset('images/CSWDO.webp') }}" alt="CSWDO Logo"
            class="landing-logo w-75px h-75px bg-white rounded-pill" />
        <span class="d-flex flex-column justify-content-center align-items-start">
            <p class="fs-4 fw-bold">City Social Welfare & Development Office</p>
            <p class="mb-0">Copyright &copy; {{ date('Y') }} City Government of Taguig</p>
        </span>
    </div>
    <div class="col d-flex justify-content-center align-items-center pe-10 text-white">
        <span class="d-flex flex-column justify-content-center align-items-center">
            <p class="fs-4 fw-bold">Contact Us</p>
            <p class="mb-0">Email: {{ $email ?? env('MAIL_FROM_ADDRESS') }}</p>
        </span>
    </div>
</div>
