@props(['modalId', 'buttonClass' => '', 'modalTitle', 'modalSize' => 'sm'])

<!-- Modal trigger button -->
<button type="button" class="btn {{ $buttonClass }}" data-bs-toggle="modal" data-bs-target="#{{ $modalId }}">
    {{ $triggerButton }}
</button>

<!-- Modal Body -->
<!-- if you want to close by clicking outside the modal, delete the last endpoint:data-bs-backdrop and data-bs-keyboard -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
    role="dialog" aria-labelledby="{{ Str::title(str_replace('-', ' ', $modalId)) }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-{{ $modalSize }}" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="{{ Str::title(str_replace('-', ' ', $modalId)) }}">
                    {{ $modalTitle }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">{{ $slot }}</div>
            <div class="modal-footer">
                {{ $footer }}
            </div>
        </div>
    </div>
</div>
