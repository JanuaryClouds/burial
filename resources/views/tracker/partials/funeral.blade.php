@if (isset($data->client?->beneficiary))
    @include('client.partial.beneficiary-info', [
        'client' => $data->client,
    ])
@else
    <p class="card-text">No Beneficiary</p>
@endif
<hr>
<h2>Assistance Status</h2>
<div class="row">
    <div class="col col-12 col-md-12 col-lg-4">
        <x-form-input name="application_date" readonly="true"
            value="{{ $data->client ? \Carbon\Carbon::parse($data->client?->created_at)->format('F d, Y') : 'N/A' }}"
            label="Date of Application" />
    </div>
    <div class="col col-12 col-md-6 col-lg-4">
        <x-form-input name="approval_date" readonly="true"
            value="{{ $data->approval_date ? \Carbon\Carbon::parse($data->approval_date)->format('F d, Y') : 'Pending' }}"
            label="Date of Approval" />
    </div>
    <div class="col col-12 col-md-6 col-lg-4">
        <x-form-input name="forward_date" readonly="true"
            value="{{ $data->forwarded_at ? \Carbon\Carbon::parse($data->forwarded_at)->format('F d, Y') : 'Pending' }}"
            label="Forwarded to Taguig Public Cemetery Staff" />
    </div>
</div>
