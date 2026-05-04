<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="mb-0">Assistance Actions</h1>
            <div class="d-flex align-items-center gap-3">
                @if (auth()->user()?->id == $data->client?->user_id || auth()->user()?->roles()->exists())
                    <a href="{{ route('client.show', ['client' => $data->client]) }}" class="btn btn-secondary mr-2"
                        data-no-loader>
                        Go to Client Record
                    </a>
                @endif
                @if (auth()->user()->roles()->exists())
                    <a href="{{ route('funeral.certificate', ['id' => $data->id]) }}" class="btn btn-info mr-2"
                        target="_blank">
                        View Certificate
                    </a>
                @endif
                @if ($data->approved_at == null)
                    <a href="{{ route('funeral.approved', ['id' => $data->id]) }}" class="btn btn-success mr-2">
                        Approved
                    </a>
                @endif
                @if ($data->approved_at !== null && $data->forwarded_at === null)
                    <a href="{{ route('funeral.forwarded', ['id' => $data->id]) }}" class="btn btn-primary">
                        Forwarded
                    </a>
                @elseif ($data->approved_at !== null && $data->forwarded_at !== null)
                    <button type="button" class="btn btn-secondary" disabled data-bs-toggle="tooltip"
                        data-bs-placement="bottom" title="Libreng Libing has been provided">
                        Forwarded
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
