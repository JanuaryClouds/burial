@php
    $assessment = $application->assessment ?? null;
@endphp
<form action="{{ route('assessment.store', $application) }}" method="POST" id="assessment-form">
    @csrf
    @method('POST')
    <h5 class="card-title">IV. ASSESSMENT</h5>
    <div class="row">
        <div class="col">
            @include('components.form-textarea', [
                'name' => 'problem_presented',
                'label' => 'a. Problem Presented',
                'value' => $assessment?->problem_presented,
                'readonly' => isset($assessment),
                'required' => true,
            ])
        </div>
    </div>
    <div class="row">
        <div class="col">
            @include('components.form-textarea', [
                'name' => 'swa',
                'label' => 'b. Social Worker\'s Assessment',
                'value' => $assessment?->assessment,
                'readonly' => isset($assessment),
                'required' => true,
            ])
        </div>
    </div>
</form>
