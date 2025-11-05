<h5 class="card-title">V. RECOMMENDED SERVICES AND ASSISTANCE</h5>
<div class="row">
    <h6 class="mb-2">Nature of Services/Assistance</h6>
    <div class="col">
        @foreach ($assistances as $id => $name)
            <label class="form-check form-check-custom form-check-solid mb-4">
                <input class="form-check-input" type="checkbox" name="assistances[]" value="{{ $id }}"/>
                <span class="form-check-label">
                    {{ Str::title($name) }}
                </span>
            </label>
        @endforeach
    </div>
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