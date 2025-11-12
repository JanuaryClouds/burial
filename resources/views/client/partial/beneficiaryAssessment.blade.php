@props([
    'client',
    'readonly' => false,
])
@php
    if (isset($client)) {
        $assessment = $client->assessment->first();
    }
@endphp
<h5 class="card-title">IV. ASSESSMENT</h5>
<div class="row">
    <div class="col">
        @include('components.form-textarea', [
            'name' => 'problem_presented',
            'label' => 'a. Problem Presented',
            'value' => $assessment->problem_presented ?? null,
            'readonly' => $assessment,
            'required' => true
        ])
    </div>
</div>
<div class="row">
    <div class="col">
        @include('components.form-textarea', [
            'name' => 'assessment',
            'label' => 'b. Social Worker\'s Assessment',
            'value' => $assessment->assessment ?? null,
            'readonly' => $assessment,
            'required' => true
        ])
    </div>
</div>