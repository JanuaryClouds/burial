<div class="row">
    <div class="col">
        @include('components.form-input', [
            'type' => 'datetime-local',
            'name' => 'approved_at',
            'label' => 'Approved at',
            'value' => $data->approved_at,
            'disabled' => !(!$readonly && isset($data->approved_at)),
            'readonly' => !(!$readonly && isset($data->approved_at)),
        ])
    </div>
    <div class="col">
        @include('components.form-input', [
            'type' => 'datetime-local',
            'name' => 'forwarded_at',
            'label' => 'Forwarded At',
            'value' => $data->forwarded_at,
            'disabled' => !(!$readonly && isset($data->forwarded_at)),
            'readonly' => !(!$readonly && isset($data->forwarded_at)),
        ])
    </div>
</div>
