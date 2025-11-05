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
        @if (app()->isLocal())
            <button class="btn btn-outline-primary" id="autofillBtn">
                <i class="fas fa-wand-magic-sparkles"></i>
            </button>
        @endif
    </div>
</div>

<div class="card shadow">
    <form action="{{ route('test.component.post') }}" method="post" id="gisForm">
        @csrf
        <div class="card-header card-header-stretch">
            <h3 id="tab-title" class="card-title">Title</h3>
            <div class="card-toolbar">
                <ul class="nav nav-tabs nav-line-tabs nav-stretch fs-6 border-0">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#kt_tab_pane_1">Client Info</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_2">Beneficiary Info</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_3">Beneficiary's Family</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_4">Assessment</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#kt_tab_pane_5">Service</a>
                    </li>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-floppy-disk"></i>
                    </button>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <div class="tab-content" id="gisTabContent">
                <div class="tab-pane fade show active" id="kt_tab_pane_1" role="tabpanel">
                    @include('client.partial.clientInfo')
                </div>
                <div class="tab-pane fade" id="kt_tab_pane_2" role="tabpanel">
                    @include('client.partial.beneficiaryInfo')
                </div>
                <div class="tab-pane fade" id="kt_tab_pane_3" role="tabpanel">
                    @include('client.partial.beneficiaryFam')
                </div>
                <div class="tab-pane fade" id="kt_tab_pane_4" role="tabpanel">
                    @include('client.partial.beneficiaryAssessment')
                </div>
                <div class="tab-pane fade" id="kt_tab_pane_5" role="tabpanel">
                    @include('client.partial.recommendedAssistance')
                </div>
            </div>
        </div>
    </form>
</div>