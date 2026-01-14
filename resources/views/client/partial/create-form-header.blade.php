<div class="card mb-4">
    <div class="card-header mt-4 mx-4 d-lg-flex justify-content-between align-items-center d-sm-none">
        <img src="{{ asset('images/city_logo.webp') }}" alt="Taguig City Logo" style="width: 150px;"
            class="text-center d-none d-md-block d-lg-block">
        <div class="d-flex flex-column align-items-center text-center text-uppercase">
            <h5 class="fs-2">City Government of Taguig</h5>
            <h4 class="fs-1">General Intake Sheet</h4>
            <h5 class="fs-3">City Social Welfare & Development Office</h5>
            <p class="text-muted">{{ Carbon\Carbon::now()->format('m/d/Y') }}</p>
        </div>
        <img src="{{ asset('images/CSWDO.webp') }}" alt="CSWDO Logo" style="width: 150px;"
            class="text-center d-none d-md-block d-lg-block">
    </div>
    <div class="card-body">
        <p>Please fill out all required forms marked by (*). These information are necessary for evaluating your
            application and to determine the type of assistance.</p>
        <div class="d-flex justify-content-between align-items-center">
            @if (env('APP_DEBUG'))
                <button class="btn btn-outline-primary" id="autofillBtn">
                    <i class="fas fa-wand-magic-sparkles"></i>
                </button>
            @endif
        </div>
        <div id="confirm-exit-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <p class="fs-5">Are you sure you want to cancel the application? You will have to fill up the
                            form again if
                            you
                            leave this page.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                            Continue Application
                        </button>
                        <a href="{{ route('landing.page') }}" class="btn btn-danger">
                            Cancel and Exit
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="confirmSubmitModal" tabindex="-1" data-bs-backdrop="static"
            data-bs-keyboard="false" role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <p class="fs-5">
                            Are you sure you want to submit your application? Once submitted, you will not be able to
                            edit your information.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                        <button type="button" class="btn btn-success" id="confirmSubmit">Confirm</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
