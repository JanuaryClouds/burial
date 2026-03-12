@props([
    'application' => [],
    'disabled' => false,
    'readonly' => false,
])
<h2>Social Worker's Assessment</h2>
@include('components.form-textarea', [
    'name' => 'swa',
    'label' => 'Social Worker\'s Assessment',
    'value' => $application->swa ?? null,
    'readonly' => $application->swa,
])
