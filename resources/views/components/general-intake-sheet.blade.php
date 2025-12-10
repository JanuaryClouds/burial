{{-- ! DEPRACATED --}}
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
        <p>Please fill out all required forms marked by (*). These information are necessary for evaluating your
            application and to determine the type of assistance.</p>
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

<div class="card shadow">
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
                    @can('assess-applications')
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#assessment_tab">Assessment</a>
                        </li>
                    @endcan
                    @can('recommend-services')
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#service_tab">Service</a>
                        </li>
                    @endcan
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
                @can('assess-applications')
                    <div class="tab-pane fade" id="assessment_tab" role="tabpanel">
                        @include('client.partial.beneficiary-assessment')
                    </div>
                @endcan
                @can('recommend-services')
                    <div class="tab-pane fade" id="service_tab" role="tabpanel">
                        @include('client.partial.recommended-assistance')
                    </div>
                @endcan
            </div>
        </div>
    </form>
</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const gisForm = document.getElementById('gisForm');
        const documentsTab = gisForm.querySelector('#documents_tab');
        const submitFormBtn = document.getElementById('submitGISForm');
        const submitModal = document.getElementById('confirmSubmitModal');
        const navLinks = document.querySelectorAll('.nav-link[data-bs-toggle="tab"]');
        const religionField = document.querySelector('select[name="religion_id"]');

        const observer = new MutationObserver(() => {
            const religion = religionField.value;
            if (documentsTab.classList.contains('active') || documentsTab.classList.contains('show')) {
                if (religion == 2) {
                    documentsTab.querySelector('#muslim-requirements').classList.remove('d-none');
                } else {
                    documentsTab.querySelector('#muslim-requirements').classList.add('d-none');
                }
            }
        });

        observer.observe(documentsTab, {
            attributes: true,
            attributeFilter: ['class']
        });

        // Create or reuse an alert element
        const alertBox = document.createElement('div');
        alertBox.className = 'alert alert-danger d-none mt-3';
        gisForm.prepend(alertBox);

        // Function to check required inputs in a given tab pane
        function validateTab(tabPane) {
            const requiredFields = tabPane.querySelectorAll('[required]');
            const missingFields = [];

            requiredFields.forEach(field => {
                // Trim string values to detect empty text inputs
                const value = field.value?.trim();
                if (!value) {
                    const label = field.closest('div')?.querySelector('label')?.innerText || field.name;
                    missingFields.push(label.replace('*', '').trim());
                }
            });

            return missingFields;
        }

        // Handle tab switching
        navLinks.forEach(link => {
            link.addEventListener('show.bs.tab', e => {
                const currentTab = document.querySelector('.tab-pane.active.show');
                const missing = validateTab(currentTab);

                if (missing.length > 0) {
                    e.preventDefault(); // stop switching
                    alertBox.innerHTML = `
                    <strong>Missing required fields:</strong>
                    <br>
                    • ${missing.join('<br>• ')}
                `;
                    alertBox.classList.add('bg-danger', 'text-white');
                    alertBox.classList.remove('d-none');
                    alertBox.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                } else {
                    alertBox.classList.add('d-none');
                }
            });
        });

        // Handle form submission manually
        submitFormBtn.addEventListener('click', e => {
            e.preventDefault();

            const allTabs = document.querySelectorAll('.tab-pane');
            let allMissing = [];

            function formatTabId(tabId) {
                return tabId
                    .replace(/_/g, ' ')
                    .replace('tab', '')
                    .replace(/\b\w/g, c => c.toUpperCase());
            }

            allTabs.forEach(tab => {
                const missing = validateTab(tab);
                if (missing.length > 0) {
                    const tabName = formatTabId(tab.id);
                    allMissing.push(...missing.map(field => `${tabName}: ${field}`));
                }
            });

            if (allMissing.length > 0) {
                alertBox.innerHTML =
                    `<strong>Cannot submit form.</strong><br>Missing fields:<br>• ${allMissing.join('<br>• ')}`;
                alertBox.classList.add('bg-danger', 'text-white');
                alertBox.classList.remove('d-none');
                alertBox.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            } else {
                alertBox.classList.add('d-none');
                const modal = new bootstrap.Modal(submitModal);
                modal.show();
                const confirmButton = submitModal.querySelector('#confirmSubmit');
                confirmButton.addEventListener('click', () => {
                    // gisForm.submit();
                    console.log('Form submitted'); // For testing purposes
                });
            }
        });
    });
</script>
