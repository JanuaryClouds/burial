@extends('layouts.metronic.guest')
<title>General Intake Form</title>
@section('content')
    <div class="h-100 d-flex flex-column justify-content-center align-items-center w-lg-75 px-5 mx-auto my-0">
        <!--div class="container min-vh-100 d-flex justify-content-center align-items-center"-->
        <div class="row my-10">
            <!--div class="col"-->
            @include('client.partial.create-form-header')
            @include('client.partial.create-form-body')
            <!--/div-->
        </div>
        <!--/div-->
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

            function validateInputs(tabPane) {
                const requiredFields = tabPane.querySelectorAll('[required]');
                requiredFields.forEach(field => {
                    if (field.value.trim() === '') {
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
                    });
                }
            });
        });
    </script>
@endsection
