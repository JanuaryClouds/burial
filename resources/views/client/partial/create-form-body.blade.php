<div class="card">
    <form action="{{ route('general.intake.form.store') }}" method="post" id="gisForm" enctype="multipart/form-data">
        @csrf
        <div class="card-header card-header-stretch">
            <h3 id="tab-title" class="card-title">General Intake Sheet</h3>
            <div class="card-toolbar">
                <ul class="nav nav-tabs nav-line-tabs nav-stretch fs-6 border-0">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#client_info_tab">Client Info</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#beneficiary_info_tab">Beneficiary Info</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#beneficiary_fam_tab">Beneficiary's Family</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#documents_tab">Documents</a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <div class="tab-content" id="gisTabContent">
                <div class="tab-pane fade show active" id="client_info_tab" role="tabpanel">
                    @include('client.partial.client-info')
                </div>
                <div class="tab-pane fade" id="beneficiary_info_tab" role="tabpanel">
                    @include('client.partial.beneficiary-info')
                </div>
                <div class="tab-pane fade" id="beneficiary_fam_tab" role="tabpanel">
                    @include('client.partial.beneficiary-fam')
                </div>
                <div class="tab-pane fade" id="documents_tab" role="tabpanel">
                    @include('client.partial.documents')
                </div>
            </div>
        </div>
    </form>
</div>