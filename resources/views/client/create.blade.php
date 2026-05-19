@extends('layouts.app')
@section('content')
    <div class="d-flex flex-column gap-4">
        <div id="confirm-exit-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="confirmExitModalLabel"
            aria-hidden="true">
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
                        <a href="{{ route('dashboard') }}" class="btn btn-danger">
                            Cancel and Exit
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="confirmSubmitModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
            role="dialog" aria-labelledby="modalTitleId" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmExitModalLabel">Cancel Application</h5>
                    </div>
                    <div class="modal-body">
                        <p class="fs-5">
                            Are you sure you want to submit your application? Once submitted, you will not be able to
                            edit your information.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            Go Back
                        </button>
                        <button type="button" class="btn btn-success" id="confirmSubmit">Confirm</button>
                    </div>
                </div>
            </div>
        </div>

        @include('client.partials.create-form-header')
        @include('client.partials.create-form-body')
        <script nonce="{{ $nonce ?? '' }}">
            document.addEventListener('DOMContentLoaded', () => {
                const gisForm = document.getElementById('gisForm');
                const documentsTab = gisForm.querySelector('#documents_tab');
                const submitModal = document.getElementById('confirmSubmitModal');
                const navLinks = document.querySelectorAll('.nav-link[data-bs-toggle="tab"]');
                const religionField = document.querySelector('select[name="ben_religion_id"]');

                const observer = new MutationObserver(() => {
                    const religion = religionField?.value;
                    if (documentsTab.classList.contains('active') || documentsTab.classList.contains('show')) {
                        const items = documentsTab.querySelectorAll('.muslim-requirements');
                        items.forEach(item => {
                            if (religion == 2) {
                                item.classList.remove('d-none');
                            } else {
                                item.classList.add('d-none');
                            }
                        });
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
                        const isCheckOrRadio = field.type === 'checkbox' || field.type === 'radio';
                        const isEmpty = isCheckOrRadio ? !field.checked : !field.value?.trim();
                        if (isEmpty) {
                            const label = field.closest('div')?.querySelector('label')?.innerText || field.name;
                            missingFields.push(label.replace('*', '').trim());
                        }
                    });

                    return missingFields;
                }

                function validateInputs(tabPane) {
                    const requiredFields = tabPane.querySelectorAll('[required]');
                    requiredFields.forEach(field => {
                        const isCheckOrRadio = field.type === 'checkbox' || field.type === 'radio';
                        const isEmpty = isCheckOrRadio ? !field.checked : !field.value?.trim();
                        if (isEmpty) {
                            field.classList.add('is-invalid');
                        } else {
                            field.classList.remove('is-invalid');
                        }
                    });
                }

                // Handle tab switching
                navLinks.forEach(link => {
                    link.addEventListener('show.bs.tab', e => {
                        const targetTabId = link.getAttribute('href');
                        const currentTab = document.querySelector('.tab-pane.active.show');

                        // If switching to the Review tab, validate all previous tabs
                        if (targetTabId === '#review_tab') {
                            const allPriorTabs = Array.from(document.querySelectorAll('.tab-pane'))
                                .filter(tab => tab.id !== 'review_tab');
                            let allMissing = [];

                            function formatTabId(tabId) {
                                return tabId
                                    .replace(/_/g, ' ')
                                    .replace('tab', '')
                                    .replace(/\b\w/g, c => c.toUpperCase());
                            }

                            allPriorTabs.forEach(tab => {
                                const missing = validateTab(tab);
                                validateInputs(tab);
                                if (missing.length > 0) {
                                    const tabName = formatTabId(tab.id);
                                    allMissing.push(...missing.map(field =>
                                        `${tabName}: ${field}`));
                                }
                            });

                            if (allMissing.length > 0) {
                                e.preventDefault(); // stop switching
                                alertBox.innerHTML = `
                                    <strong>Cannot view review page yet.</strong>
                                    <br>
                                    Please complete the required fields in previous sections first:
                                    <br>
                                    • ${allMissing.join('<br>• ')}
                                `;
                                alertBox.classList.add('bg-danger', 'text-white');
                                alertBox.classList.remove('d-none');
                                alertBox.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'start'
                                });
                                return;
                            }
                        } else {
                            // Standard validation for current tab only when moving to other tabs
                            const missing = validateTab(currentTab);
                            validateInputs(currentTab);

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
                                return;
                            }
                        }

                        alertBox.classList.add('d-none');
                    });
                });

                // Function to dynamically populate the review page
                function populateReviewTab() {
                    const textVal = (name) => {
                        const el = document.querySelector(`[name="${name}"]`);
                        return el ? el.value.trim() || '-' : '-';
                    };

                    const selectText = (name) => {
                        const el = document.querySelector(`select[name="${name}"]`);
                        if (!el) return '-';
                        if (el.selectedIndex < 0) return '-';
                        const text = el.options[el.selectedIndex]?.text;
                        return (text && text !== 'Select one') ? text : '-';
                    };

                    // Client Info
                    document.getElementById('rev_first_name').innerText = textVal('first_name');
                    document.getElementById('rev_middle_name').innerText = textVal('middle_name');
                    document.getElementById('rev_last_name').innerText = textVal('last_name');
                    document.getElementById('rev_suffix').innerText = textVal('suffix');
                    document.getElementById('rev_age').innerText = textVal('age');
                    document.getElementById('rev_sex').innerText = selectText('sex_id');
                    document.getElementById('rev_date_of_birth').innerText = textVal('date_of_birth');
                    document.getElementById('rev_contact_number').innerText = textVal('contact_number');
                    document.getElementById('rev_relationship').innerText = selectText('relationship_id');
                    document.getElementById('rev_civil').innerText = selectText('civil_id');
                    document.getElementById('rev_religion').innerText = selectText('religion_id');
                    document.getElementById('rev_nationality').innerText = selectText('nationality_id');
                    document.getElementById('rev_education').innerText = selectText('education_id');
                    document.getElementById('rev_skill').innerText = textVal('skill');
                    document.getElementById('rev_income').innerText = textVal('income');
                    document.getElementById('rev_philhealth').innerText = textVal('philhealth');

                    // Client Address
                    const houseNo = textVal('house_no');
                    const street = textVal('street');
                    const barangay = selectText('barangay_id');
                    let clientAddress = '';
                    if (houseNo !== '-') clientAddress += houseNo + ' ';
                    if (street !== '-') clientAddress += street + ', ';
                    if (barangay !== '-') clientAddress += barangay;
                    document.getElementById('rev_address').innerText = clientAddress || '-';

                    const districtVal = textVal('district_id');
                    document.getElementById('rev_district').innerText = districtVal ? 'District ' + districtVal : '-';
                    document.getElementById('rev_city').innerText = 'Taguig City';

                    // Beneficiary Info
                    document.getElementById('rev_ben_first_name').innerText = textVal('ben_first_name');
                    document.getElementById('rev_ben_middle_name').innerText = textVal('ben_middle_name');
                    document.getElementById('rev_ben_last_name').innerText = textVal('ben_last_name');
                    document.getElementById('rev_ben_suffix').innerText = textVal('ben_suffix');
                    document.getElementById('rev_ben_sex').innerText = selectText('ben_sex_id');
                    document.getElementById('rev_ben_date_of_birth').innerText = textVal('ben_date_of_birth');
                    document.getElementById('rev_ben_date_of_death').innerText = textVal('ben_date_of_death');
                    document.getElementById('rev_ben_religion').innerText = selectText('ben_religion_id');
                    document.getElementById('rev_ben_place_of_birth').innerText = textVal('ben_place_of_birth');
                    document.getElementById('rev_ben_barangay').innerText = selectText('ben_barangay_id');

                    // Family Composition
                    const famNames = document.querySelectorAll('input[name="fam_name[]"]');
                    const famSexes = document.querySelectorAll('select[name="fam_sex_id[]"]');
                    const famAges = document.querySelectorAll('input[name="fam_age[]"]');
                    const famCivils = document.querySelectorAll('select[name="fam_civil_id[]"]');
                    const famRelationships = document.querySelectorAll('select[name="fam_relationship_id[]"]');
                    const famOccupations = document.querySelectorAll('input[name="fam_occupation[]"]');
                    const famIncomes = document.querySelectorAll('input[name="fam_income[]"]');

                    let familyHtml = '';
                    let hasFamily = false;

                    famNames.forEach((input, index) => {
                        const name = input.value?.trim();
                        if (!name) return; // skip completely empty rows

                        hasFamily = true;
                        const sexSelect = famSexes[index];
                        const sex = sexSelect ? sexSelect.options[sexSelect.selectedIndex]?.text : '-';

                        const age = famAges[index]?.value || '-';

                        const civilSelect = famCivils[index];
                        const civil = civilSelect ? civilSelect.options[civilSelect.selectedIndex]?.text : '-';

                        const relSelect = famRelationships[index];
                        const relationship = relSelect ? relSelect.options[relSelect.selectedIndex]?.text : '-';

                        const occupation = famOccupations[index]?.value || '-';
                        const income = famIncomes[index]?.value || '-';

                        familyHtml += `
                            <tr>
                                <td><div class="fw-bold text-gray-800">${name}</div></td>
                                <td><span class="badge badge-light-primary">${relationship}</span></td>
                                <td>${sex}</td>
                                <td>${age}</td>
                                <td>${civil}</td>
                                <td>${occupation}</td>
                                <td>${income}</td>
                            </tr>
                        `;
                    });

                    const tbody = document.getElementById('rev_family_table_body');
                    if (hasFamily) {
                        tbody.innerHTML = familyHtml;
                    } else {
                        tbody.innerHTML =
                            `<tr><td colspan="7" class="text-center text-muted py-4">No family members listed.</td></tr>`;
                    }

                    // Clean up any old object URLs to avoid memory leaks
                    if (window.activeObjectUrls) {
                        window.activeObjectUrls.forEach(url => URL.revokeObjectURL(url));
                    }
                    window.activeObjectUrls = [];

                    // Documents and Previews
                    const fileInputs = document.querySelectorAll('#documents_tab input[type="file"]');
                    const docsContainer = document.getElementById('rev_documents_container');
                    let docsHtml = '';
                    let hasVisibleDocs = false;

                    fileInputs.forEach(input => {
                        // Check if parent has d-none class (hidden Muslim requirements)
                        const muslimWrapper = input.closest('.muslim-requirements');
                        if (muslimWrapper && muslimWrapper.classList.contains('d-none')) {
                            return;
                        }

                        hasVisibleDocs = true;
                        const parent = input.closest('.custom-file');
                        let label = parent ? parent.querySelector('label')?.innerText : input.name;
                        // Clean label text
                        if (label) {
                            label = label.replace(/\s*\*$/, '').trim();
                        } else {
                            label = 'Document';
                        }

                        const file = input.files ? input.files[0] : null;

                        if (file) {
                            const objectUrl = URL.createObjectURL(file);
                            window.activeObjectUrls.push(objectUrl);

                            docsHtml += `
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border border-solid border-gray-300 rounded shadow-sm bg-white">
                                        <div class="card-body p-4 d-flex flex-column align-items-center text-center">
                                            <span class="badge badge-light-success mb-3 fw-bold"><i class="fa fa-check-circle text-success me-1"></i> Attached</span>
                                            <h6 class="text-gray-800 fw-bolder mb-1">${label}</h6>
                                            <span class="text-muted fs-7 mb-4 text-break">${file.name}</span>
                                            <div class="w-100 rounded overflow-hidden bg-light d-flex align-items-center justify-content-center border" style="height: 150px;">
                                                <img src="${objectUrl}" class="img-fluid" style="max-height: 150px; object-fit: contain;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        } else {
                            docsHtml += `
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border border-dashed border-gray-300 bg-light bg-opacity-20 rounded">
                                        <div class="card-body p-4 d-flex flex-column align-items-center justify-content-center text-center">
                                            <span class="badge badge-light-warning mb-3 fw-bold"><i class="fa fa-info-circle text-warning me-1"></i> Optional</span>
                                            <h6 class="text-gray-500 fw-bolder mb-1">${label}</h6>
                                            <span class="text-muted fs-7">No file chosen</span>
                                        </div>
                                    </div>
                                </div>
                            `;
                        }
                    });

                    if (hasVisibleDocs) {
                        docsContainer.innerHTML = docsHtml;
                    } else {
                        docsContainer.innerHTML =
                            `<div class="col-12 text-center text-muted py-4">No documents available to submit.</div>`;
                    }
                }

                // Listen for when review tab is shown
                const reviewTabLink = document.getElementById('review_tab_link');
                if (reviewTabLink) {
                    reviewTabLink.addEventListener('shown.bs.tab', () => {
                        populateReviewTab();
                    });
                }

                // Hook up the Confirm & Submit button from the Review tab
                const submitFormBtn = document.getElementById('confirmSubmitFromReview');
                if (submitFormBtn) {
                    // Handle form submission manually
                    submitFormBtn.addEventListener('click', e => {
                        e.preventDefault();

                        const allTabs = Array.from(document.querySelectorAll('.tab-pane')).filter(tab => tab
                            .id !==
                            'review_tab');
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
                                confirmButton.classList.add('disabled');
                                confirmButton.disabled = true;
                                confirmButton.innerHTML = 'Submitting...';
                                gisForm.submit();
                            }, {
                                once: true
                            });
                        }
                    });
                }
            });
        </script>
    </div>
@endsection
