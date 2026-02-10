@props(['client', 'readonly' => false])
@php
    $types = [
        'burial' => 'Burial Assistance',
        'funeral' => 'Libreng Libing',
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
                <input class="form-check-input" type="checkbox" name="assistances[]" value="{{ $id }}"
                    {{ in_array($name, ['Burial']) ? 'checked disabled' : 'disabled' }} />
                <span class="form-check-label">
                    {{ Str::title($name) }}
                </span>
            </label>
        </div>
    @endforeach
    <div class="col">
        @include('components.form-select', [
            'name' => 'type',
            'id' => 'type',
            'label' => 'Type of Assistance',
            'options' => $types,
            'selected' => $recommendedAssistance->type ?? null,
            'disabled' => $recommendedAssistance,
        ])
        <div id="referral">
            @include('components.form-input', [
                'name' => 'referral',
                'id' => 'referralField',
                'label' => 'Referral',
                'value' => $recommendedAssistance->referral ?? null,
                'readonly' => $recommendedAssistance,
            ])
        </div>
        <div id="amount">
            @include('components.form-input', [
                'name' => 'amount',
                'id' => 'amountField',
                'min' => 0,
                'label' => 'Amount of Assistance to be Extended',
                'type' => 'number',
                'value' => $recommendedAssistance->amount ?? null,
                'readonly' => $recommendedAssistance,
            ])
        </div>
        <div id="moa">
            @include('components.form-select', [
                'name' => 'moa_id',
                'id' => 'moaField',
                'label' => 'Mode of Assistance',
                'options' => $modes,
                'selected' => $recommendedAssistance->moa_id ?? null,
                'disabled' => $recommendedAssistance,
            ])
        </div>
        @include('components.form-textarea', [
            'name' => 'remarks',
            'label' => 'Remarks',
            'value' => $recommendedAssistance->remarks ?? null,
            'readonly' => $recommendedAssistance,
        ])
    </div>
</div>
<script>
    const typeField = document.getElementById('type');
    const referralField = document.getElementById('referralField');
    const amountContainer = document.getElementById('amount');
    const amountField = document.getElementById('amountField');
    const moaContainer = document.getElementById('moa');
    const moaField = document.getElementById('moaField');

    typeField.addEventListener('change', function() {
        if (typeField.value == 'funeral') {
            referralField.value = "Taguig City Public Cemetery";
            amountContainer.classList.add('d-none');
            moaContainer.classList.add('d-none');
            amountField.removeAttribute('required');
            moaField.removeAttribute('required');
        }

        if (typeField.value == 'burial') {
            referralField.value = "";
            amountContainer.classList.remove('d-none');
            moaContainer.classList.remove('d-none');
            amountField.setAttribute('required', 'true');
            moaField.setAttribute('required', 'true');
        }
    })
</script>
