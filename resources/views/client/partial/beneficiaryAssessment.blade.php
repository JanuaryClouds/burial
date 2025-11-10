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
        <x-form-textarea 
            name="problem_presented"
            label="a. Problem Presented"
            value="{{ $assessment->problem_presented ?? null }}"
            :readonly="$readonly"
            required
        />
    </div>
    <div class="col">
        <x-form-textarea
            name="assessment"
            label="b. Social Worker's Assessment"
            value="{{ $assessment->assessment ?? null }}"
            :readonly="$readonly"
            required
        />
    </div>
</div>