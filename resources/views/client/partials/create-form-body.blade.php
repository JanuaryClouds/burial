<div class="card">
    <form action="{{ route('general.intake.form.store') }}" method="post" id="gisForm" enctype="multipart/form-data">
        @csrf
        <div class="card-header card-header-stretch">
            <h3 id="tab-title" class="card-title">General Intake Sheet</h3>
            <div class="card-toolbar">
                <ul class="nav nav-tabs nav-line-tabs nav-stretch fs-6 border-0" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#client_info_tab" role="tab"
                            aria-controls="client_info_tab" aria-selected="true">Client Info</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#beneficiary_info_tab" role="tab"
                            aria-controls="beneficiary_info_tab" aria-selected="false">Beneficiary Info</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#beneficiary_fam_tab" role="tab"
                            aria-controls="beneficiary_fam_tab" aria-selected="false">Beneficiary's Family</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#documents_tab" role="tab"
                            aria-controls="documents_tab" aria-selected="false">Documents</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <div class="tab-content" id="gisTabContent">
                <div class="tab-pane fade show active" id="client_info_tab" role="tabpanel"
                    aria-labelledby="client_info_tab_link">
                    @include('client.partials.client-info')
                </div>
                <div class="tab-pane fade" id="beneficiary_info_tab" role="tabpanel"
                    aria-labelledby="beneficiary_info_tab_link">
                    @include('client.partials.beneficiary-info')
                </div>
                <div class="tab-pane fade" id="beneficiary_fam_tab" role="tabpanel" aria-labelledby="family_tab_link">
                    @include('client.partials.beneficiary-fam')
                </div>
                <div class="tab-pane fade" id="documents_tab" role="tabpanel" aria-labelledby="documents_tab_link">
                    @include('client.partials.documents')
                </div>
            </div>
        </div>
    </form>
</div>
