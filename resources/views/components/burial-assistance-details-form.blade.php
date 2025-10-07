@props([
    "burialAssistance",
    "readonly" => false,
    "disabled" => false
])
<div
    class="bg-white shadow-sm rounded p-4"
>
    <h2>Additional Details</h2>
    <div
        class="row flex-column justify-content-center align-items-end g-2"
    >
        <div class="col">
            <x-form-input 
                name="funeraria"
                label="Funeraria"
                required="true"
                value="{{ $burialAssistance->funeraria ?? '' }}"
                readonly="{{ $readonly }}"
                disabled="{{ $disabled }}"
            />
        </div>
        <div class="col">
            <x-form-input 
                name="amount"
                label="Amount"
                type="number"
                required="true"
                value="{{ $burialAssistance->amount ?? '' }}"
                readonly="{{ $readonly }}"
                disabled="{{ $disabled }}"
            />
        </div>
        <div class="col">
            <x-form-textarea 
                name="remarks"
                label="Remarks"
                value="{{ $burialAssistance->remarks ?? '' }}"
                readonly="{{ $readonly }}"
                disabled="{{ $disabled }}"
            />
        </div>
    </div>
</div>
