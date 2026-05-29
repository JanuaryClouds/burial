<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <h1 class="mb-0">Assistance Actions</h1>
            <span class="d-flex align-items-center gap-2">
                @if (auth()->user()?->id == $data->originalClaimant()?->client?->user_id || auth()->user()->roles()->exists())
                    <a href="{{ route('client.show', ['client' => $data->originalClaimant()?->client]) }}"
                        class="btn btn-light mr-2" data-no-loader>
                        Go to Client Record
                    </a>
                @endif
                @if (auth()->user()->hasRole('staff'))
                    @if (app()->hasDebugModeEnabled() || ($show_certificate && auth()->user()->can('create-certificates')))
                        <a href="{{ route('burial.certificate', ['id' => $data->id]) }}" class="btn btn-light mr-2"
                            target="_blank">
                            View Certificate
                        </a>
                    @endif
                    @if ($data->status != 'rejected' && $data->status != 'released' && $next_step != null)
                        @can('update', [App\Models\BurialAssistance::class, $data])
                            <button class="btn btn-primary mr-2" type="button" data-bs-toggle="modal"
                                data-bs-target="#addUpdateModal-{{ $data->id }}">
                                Add Progress Update
                            </button>
                        @else
                            <button type="button" class="btn btn-secondary" disabled data-bs-toggle="tooltip"
                                data-bs-placement="bottom" title="You do not have permissions to update this record">
                                Add Progress Update
                            </button>
                        @endcan
                    @endif
                @endif
            </span>
        </div>
    </div>
</div>
