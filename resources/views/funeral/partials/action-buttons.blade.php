<div class="row">
    <div class="d-flex justify-content-end gap-3">
        <a href="{{ route('clients.gis-form', ['id' => $data->client_id]) }}" class="btn btn-secondary mr-2" data-no-loader>
            Generate GIS Form
        </a>
        <a href="{{ route('funeral-assistances.view.certificate', ['id' => $data->id]) }}" class="btn btn-secondary mr-2" target="_blank">
            Download Certificate
        </a>
        @if ($data->approved_at == null)
            <a href="{{ route('funeral-assistances.view.approved', ['id' => $data->id]) }}" class="btn btn-success mr-2">
                <i class="fas fa-check-circle"></i>
                Approved
            </a>
        @endif
        @if (!$data->approved_at == null && $data->forwarded_at == null)
            <a href="{{ route('funeral-assistances.view.forwarded', ['id' => $data->id]) }}" class="btn btn-primary">
                <i class="fas fa-forward"></i>
                Forwarded
            </a>
        @endif
    </div>
</div>