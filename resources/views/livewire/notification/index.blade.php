<div class="card">
    <div class="card-header d-flex align-items-center justify-content-between gap-1">
        <span class="d-flex align-items-center gap-1">
            <h4 class="card-title">Notifications</h4>
            <p class="text-muted fw-bold mb-0">{{ $unreadCount }} unread</p>
        </span>
        <p class="text-muted" wire:loading>...</p>
    </div>
    <div class="card-body h-350px overflow-y-scroll" wire:poll.30s='$refresh'>
        @if (count($notifications) > 0)
            @foreach ($notifications as $notification)
                <div wire:key="notification-{{ $notification['id'] }}">
                    <div class="row w-100">
                        <div class="col-2">
                            <span
                                class="badge rounded-pill text-bg-info d-flex align-items-center justify-content-center">
                                {{ $notification['created_at'] }}
                            </span>
                        </div>
                        <div class="col-9">
                            <div class="d-flex flex-column gap-1">
                                <span class="fw-bold">{{ $notification['subject'] }}</span>
                                <span class="text-muted">{{ $notification['source_class'] }}</span>
                            </div>
                        </div>
                        <div class="col-1" wire:loading.remove>
                            <button wire:click="markAsRead('{{ $notification['id'] }}')" type="button"
                                class="btn btn-sm read-btn btn-outline-success rounded-pill" aria-label="Mark as Read">
                                <i class="fas fa-check pe-0"></i>
                            </button>
                        </div>
                    </div>
                    @if (!$loop->last)
                        <hr />
                    @endif
                </div>
            @endforeach
        @else
            <p class="text-muted text-center">No Notifications</p>
        @endif
    </div>
    <div class="card-footer d-flex justify-content-end align-items-center gap-2">
        <span wire:loading.remove>
            <button type="button" wire:click='$refresh' class="btn btn-sm btn-secondary" wire:target='$refresh'>
                Refresh
            </button>
        </span>
        <span wire:loading>
            @include('partials.loader.bar')
        </span>
    </div>
</div>
