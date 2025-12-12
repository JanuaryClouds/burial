<ul class="list-group">
    <li class="list-group-item bg-body-secondary d-flex justify-content-between align-items-center">
        Submitted Application
        <span class="badge badge-pill p-0">{{ $burialAssistance->created_at }}</span>
    </li>
    @foreach ($claimants as $claimant)
        @foreach ($processLogs as $log)
            <li
                class="list-group-item d-flex justify-content-between align-items-center {{ $loop->last ? 'bg-primary text-white' : 'bg-body-secondary' }}">
                <p
                    class="mb-0 {{ $loop->last ? 'fw-bold text-white' : 'text-black' }} d-flex align-items-baseline gap-3">
                    <b>{{ class_basename($log->loggable) === 'WorkflowStep' ? $log->loggable?->description : $log->comments }}</b>
                    @if (class_basename($log->loggable) === 'WorkflowStep' && $log->comments)
                        <a class="ml-4 {{ $loop->last ? 'text-white' : '' }}"
                            data-target="#show-comments-{{ $log->id }}" data-toggle="collapse" aria-expanded="false"
                            aria-controls="show-comments-{{ $log->id }}">
                            <i class="fa fa-comment-alt"></i>
                        </a>
                    @endif
                    @if (class_basename($log->loggable) === 'WorkflowStep' && $log->extra_data)
                        <a class="ml-4 {{ $loop->last ? 'text-white' : '' }}"
                            data-target="#show-extra-data-{{ $log->id }}" data-toggle="collapse"
                            aria-expanded="false" aria-controls="show-comments-{{ $log->id }}">
                            <i class="fas fa-circle-info"></i>
                        </a>
                    @endif
                    @if ($burialAssistance->status == 'released' && $log?->loggable?->order_no == 13)
                        <button class="btn ml-4" type="button" data-bs-toggle="modal"
                            data-bs-target="#show-cheque-proof-{{ $log->id }}">
                            <i class="fas fa-image"></i>
                        </button>
                    @endif
                </p>
                @if (class_basename($log->loggable) === 'WorkflowStep')
                    <span class="d-flex justify-content-center align-items-center gap-3">
                        <span
                            class="badge badge-pill p-0 m-0 d-flex align-items-center {{ $loop->last ? 'text-white fw-bold' : 'text-black' }}">
                            In: {{ $log->date_in }}
                            {{ $log->date_out ? '/ Out: ' . $log->date_out : '' }}
                        </span>
                        @if (auth()->user())
                            @if (auth()->user()->can('add-updates') && $loop->last)
                                <!-- An edit function would lose data integrity. To ensure data integrity, CSWDO must delete the log and create a new one -->
                                <span class="d-flex align-items-center btn-toolbar">
                                    <x-delete-log :id="$burialAssistance->id" :stepId="$log->loggable->order_no" />
                                </span>
                            @endif
                        @endif
                    </span>
                @else
                    <span
                        class="badge badge-pill p-0 {{ $loop->last ? 'text-white fw-bold' : 'text-black' }}">{{ $log->date_in }}</span>
                @endif
            </li>
            <div id="show-comments-{{ $log->id }}" class="collapse">
                <li class="list-group-item border-top-0 {{ $loop->last ? 'bg-primary text-white' : '' }}">
                    <p class="mb-0">{{ $log->comments }}</p>
                </li>
            </div>
            @if (class_basename($log->loggable) === 'WorkflowStep' && $log->extra_data)
                <div id="show-extra-data-{{ $log->id }}" class="collapse">
                    <li class="list-group-item border-top-0 {{ $loop->last ? 'bg-primary text-white' : '' }}">
                        @foreach ($log->extra_data as $key => $subKey)
                            @if (is_array($subKey))
                                @foreach ($subKey as $data => $value)
                                    <p class="mb-0">
                                        <b>{{ ucfirst(str_replace('_', ' ', $data)) }}</b> -
                                        {{ $value }}
                                    </p>
                                @endforeach
                            @elseif (is_string($subKey))
                                <p class="mb-0">
                                    <b>{{ ucfirst(str_replace('_', ' ', $key)) }}</b> -
                                    {{ $subKey }}
                                </p>
                            @endif
                        @endforeach
                    </li>
                </div>
            @endif
            @if (class_basename($log->loggable) === 'WorkflowStep' &&
                    $log->loggable->order_no == 13 &&
                    $burialAssistance->status == 'released')
                <div id="show-cheque-proof-{{ $log->id }}" class="modal fade" tabindex="-1" role="dialog"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                                @php
                                    $encryptedFile = Storage::disk('local')->get(
                                        "burial-assistance/{$burialAssistance->tracking_no}/{$burialAssistance->latestCheque->id}-cheque-proof.png",
                                    );
                                    $file = Crypt::decrypt($encryptedFile);
                                @endphp
                                <div class="w-auto">
                                    <input type="image" src="{{ 'data:image;base64,' . base64_encode($file) }}"
                                        alt="Proof image of cheque claiming" class="img-fluid rounded">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

        @php
            $lastLogDate = $claimant->processLogs->last()?->date_in;
            $change = $claimantChanges->first(function ($c) use ($lastLogDate) {
                return $lastLogDate === null || $c->changed_at > $lastLogDate;
            });
        @endphp
        @if (
            !empty($burialAssistance->rejection()) &&
                $burialAssistance->rejection()->count() > 0 &&
                $burialAssistance->status == 'rejected')
            <li class="list-group-item d-flex justify-content-between align-items-center bg-danger">
                <p class="mb-0 text-white">Rejected: {{ $burialAssistance->rejection->reason }}</p>
                <span class="badge badge-pill p-0 text-white">{{ $burialAssistance->rejection->created_at }}</span>
            </li>
        @endif
    @endforeach
</ul>
