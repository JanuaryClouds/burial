@props([
    'client',
    'readonly' => false
])
@php
    $types = [
        'burial' => 'Burial Assistance',
        'funeral' => 'Funeral Assistance',
    ];

    if (isset($client)) {
        $recommendedAssistance = $client->recommendation->first();
    } else {
        $recommendedAssistance = null;
    }
@endphp
<h5 class="card-title">V. RECOMMENDED SERVICES AND ASSISTANCE</h5>
<h6 class="mb-2">Nature of Services/Assistance</h6>
<div class="row">
    @foreach ($assistances as $id => $name)
        <div class="col-6">
            <label class="form-check form-check-custom form-check-solid mb-4">
                <input 
                    class="form-check-input" 
                    type="checkbox" 
                    name="assistances[]" 
                    value="{{ $id }}" 
                    {{ in_array($name, ['Burial']) ? 'checked disabled' : 'disabled' }} 
                />
                <span class="form-check-label">
                    {{ Str::title($name) }}
                </span>
            </label>
        </div>
    @endforeach
    <div class="col">
        @include('components.form-input', [
            'name' => 'referral',
            'label' => 'Referral',
            'value' => $recommendedAssistance->referral ?? null,
            'readonly' => $recommendedAssistance
        ])
        @include('components.form-input', [
            'name' => 'amount',
            'label' => 'Amount of Assistance to be Extended',
            'type' => 'number',
            'value' => $recommendedAssistance->amount ?? null,
            'readonly' => $recommendedAssistance
        ])
        @include('components.form-select', [
            'name' => 'moa_id',
            'label' => 'Mode of Assistance',
            'options' => $modes,
            'selected' => $recommendedAssistance->moa_id ?? null,
            'disabled' => $recommendedAssistance
        ])
        @include('components.form-select', [
            'name' => 'type',
            'label' => 'Type of Assistance',
            'options' => $types,
            'selected' => $recommendedAssistance->type ?? null,
            'disabled' => $recommendedAssistance
        ])
        @include('components.form-textarea', [
            'name' => 'remarks',
            'label' => 'Remarks',
            'value' => $recommendedAssistance->remarks ?? null,
            'readonly' => $recommendedAssistance
        ])
    </div>
</div>