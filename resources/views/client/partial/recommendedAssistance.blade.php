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
        <x-form-input
            name="referral"
            label="Referral"
        />
        <x-form-input
            name="amount"
            label="Amount of Assistance to be Extended"
            type="number"
        />
        <x-form-select
            name="mode"
            label="Mode of Assistance"
            :options="$modes"
            required="true"
        />
    </div>
</div>