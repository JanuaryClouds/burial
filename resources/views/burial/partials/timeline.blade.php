<ul class="list-group">
    <li class="list-group-item bg-transparent bg-body-secondary d-flex justify-content-between align-items-center">
        Submitted Application
        <span class="badge badge-pill p-0">{{ $data->created_at }}</span>
    </li>
    @foreach ($timeline as $log)
        <li
            class="list-group-item d-flex justify-content-between align-items-center {{ $loop->last ? 'bg-primary text-white rounded-bottom' : 'bg-body-secondary' }}">
            <p class="mb-0 {{ $loop->last ? 'fw-bold text-white' : '' }}">
                {{ $log['step'] ? 'Step ' . $log['step'] . ':' : '' }} {{ $log['description'] ?? 'System Remark' }}
                @if (isset($log['comments']))
                    <a class="ms-4 {{ $loop->last ? 'text-white' : '' }}"
                        data-bs-target="#show-comments-{{ $log['id'] }}" data-bs-toggle="collapse" aria-expanded="false"
                        aria-controls="show-comments-{{ $log['id'] }}">
                        <i class="fa fa-comment-alt"></i>
                    </a>
                @endif
                @if (isset($log['extra_data']))
                    <a class="ms-4 {{ $loop->last ? 'text-white' : '' }}"
                        data-bs-target="#show-extra-data-{{ $log['id'] }}" data-bs-toggle="collapse"
                        aria-expanded="false" aria-controls="show-extra-data-{{ $log['id'] }}">
                        <i class="fas fa-circle-info"></i>
                    </a>
                @endif
                @if ($log['step'] == 13)
                    <button class="btn ml-4" type="button" data-bs-toggle="modal"
                        data-bs-target="#show-cheque-proof-{{ $log['id'] }}">
                        <i class="fas fa-image"></i>
                    </button>
                @endif
            </p>
            <span class="d-flex justify-content-center align-items-center gap-3">
                <span
                    class="badge badge-pill p-0 m-0 d-flex align-items-center {{ $loop->last ? 'text-white fw-bold' : '' }}">
                    In: {{ $log['in'] }}
                    {{ $log['out'] ? '/ Out: ' . $log['out'] : '' }}
                </span>
                @auth
                    @if (auth()->user()->can('add-updates') && $loop->last && !$log['loggable'] instanceof App\Models\ClaimantChange)
                        @include('burial.partials.delete-log', [
                            'id' => $log['id'],
                            'step' => $log['step'],
                        ])
                    @endif
                @endauth
            </span>
        </li>
        @if (isset($log['comments']))
            <li id="show-comments-{{ $log['id'] }}"
                class="list-group-item border-top-0 collapse {{ $loop->last ? 'bg-primary text-white rounded-bottom' : 'bg-body-secondary' }}">
                <p class="mb-0">{{ $log['comments'] }}</p>
            </li>
        @endif
        @if (isset($log['extra_data']))
            <div id="show-extra-data-{{ $log['id'] }}" class="collapse">
                <li
                    class="list-group-item border-top-0 {{ $loop->last ? 'bg-primary text-white rounded-bottom' : 'bg-body-secondary' }}">
                    @foreach ($log['extra_data'] as $key => $subKey)
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
        @if ($log['step'] == 13)
            <div id="show-cheque-proof-{{ $log['id'] }}" class="modal fade" tabindex="-1" role="dialog"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-body">
                            {{-- TODO use fileserver logic --}}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
</ul>
