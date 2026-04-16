@extends('layouts.app')
@section('content')
    <div class="d-flex flex-column gap-4">
        <div class="card">
            <div class="card-body d-flex justify-content-between align-items-center">
                <h1 class="mb-0">Actions</h1>
                <span class="d-flex justify-content-between align-items-center gap-6">
                    <button class="btn btn-light" type="button" data-bs-toggle="modal" data-bs-target="#confirm-exit-modal">
                        Cancel
                    </button>
                    <button class="btn btn-primary hover-scale text-nowrap" id="submitGISForm">
                        Submit Application
                    </button>
                </span>
            </div>
        </div>
        <div id="confirm-exit-modal" class="modal fade" tabindex="-1" role="dialog"
            aria-labelledby="confirmExitModalLabel" aria-hidden="true">
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
                const submitFormBtn = document.getElementById('submitGISForm');
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
                        const currentTab = document.querySelector('.tab-pane.active.show');
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
                            gisForm.submit();
                        }, {
                            once: true
                        });
                    }
                });
            });
        </script>
    </div>
@endsection
