<div class="card shadow mb-4">
    <div class="card-header mt-4 mx-4 d-flex justify-content-between align-items-center">
        <img src="{{ asset('images/city_logo.webp') }}" alt="Taguig City Logo" style="width: 150px;">
        <div class="d-flex flex-column align-items-center text-center text-uppercase">
            <h5 class="fs-2">City Government of Taguig</h5>
            <h4 class="fs-1">General Intake Sheet</h4>
            <h5 class="fs-3">City Social Welfare & Development Office</h5>
            <p class="text-muted">{{ Carbon\Carbon::now()->format('m/d/Y') }}</p>
        </div>
        <img src="{{ asset('images/CSWDO.webp') }}" alt="CSWDO Logo" style="width: 150px;">
    </div>
    <div class="card-body">
        <p>Please fill out all required forms marked by (*). These information are necessary for evaluating your application and to determine the type of assistance.</p>
        <div class="d-flex justify-content-between align-items-center">
            @if (app()->isLocal())
                <button class="btn btn-outline-primary" id="autofillBtn">
                    <i class="fas fa-wand-magic-sparkles"></i>
                </button>
            @endif
            <span class="d-flex justify-content-between align-items-center gap-6">
                <button class="btn btn-secondary" type="button" data-toggle="modal" data-target="#confirm-exit-modal">
                    <i class="ki-duotone ki-cross-circle">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i>
                    Cancel
                </button>
                <div id="confirm-exit-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                                <p>Are you sure you want to cancel the application? You will have to fill up the form again if you leave this page.</p>
                            </div>
                            <div class="modal-footer">
                                <a href="{{ route('landing.page') }}" class="btn btn-secondary hover-scale">
                                    <i class="ki-duotone ki-exit-left">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i> Cancel and Exit
                                </a>
                                <button type="button" class="btn btn-danger hover-scale" data-dismiss="modal">
                                    <i class="ki-duotone ki-exit-right">
                                        <span class="path1"></span>
                                        <span class="path2"></span>
                                    </i> Continue Application
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary hover-scale" id="submitGISForm">
                    <i class="ki-duotone ki-exit-up">
                        <span class="path1"></span>
                        <span class="path2"></span>
                    </i> Submit Application
                </button>
            </span>
        </div>
    </div>
</div>