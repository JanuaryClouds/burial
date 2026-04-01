@props(['client', 'readonly' => false])
@php
    $assessment = isset($client) ? $client->assessment->first() : null;
@endphp
<h5 class="card-title">IV. ASSESSMENT</h5>
<div class="row">
    <div class="col">
        @include('components.form-textarea', [
            'name' => 'problem_presented',
            'label' => 'a. Problem Presented',
            'value' => $assessment?->problem_presented,
            'readonly' => $readonly || isset($assessment),
            'required' => true,
        ])
    </div>
</div>
<div class="row">
    <div class="col">
        @include('components.form-textarea', [
            'name' => 'assessment',
            'label' => 'b. Social Worker\'s Assessment',
            'value' => $assessment?->assessment,
            'readonly' => $readonly || isset($assessment),
            'required' => true,
        ])
    </div>
</div>
