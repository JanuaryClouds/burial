@props([
    'modalId',
    'buttonLabel',
    'buttonClass',
    'modalTitle',
    'closeButtonLabel' => 'Cancel',
    'submitButtonLabel' => 'Submit',
    'modalSize' => 'sm',
    'modalKeyboard' => false,
    'modalStaticBackdrop' => true,
    'formRoute',
    'formMethod' => 'POST',
])

<!-- Modal trigger button -->
<button type="button" class="btn {{ $buttonClass }}" data-bs-toggle="modal" data-bs-target="#{{ $modalId }}">
    {{ $buttonLabel }}
</button>

<!-- Modal Body -->
<!-- if you want to close by clicking outside the modal, delete the last endpoint:data-bs-backdrop and data-bs-keyboard -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" data-bs-backdrop="{{ $modalStaticBackdrop }}"
    data-bs-keyboard="{{ $modalKeyboard }}" role="dialog"
    aria-labelledby="{{ Str::title(str_replace('-', ' ', $modalId)) }}" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-dialog-centered modal-{{ $modalSize }}" role="document">
        <div class="modal-content">
            <form action="{{ route($formRoute) }}" method="{{ $formMethod }}">
                @csrf
                @method($formMethod)
                <div class="modal-header">
                    <h5 class="modal-title" id="{{ Str::title(str_replace('-', ' ', $modalId)) }}">
                        {{ $modalTitle }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">{{ $slot }}</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        {{ $closeButtonLabel }}
                    </button>
                    <button type="submit" class="btn btn-primary">{{ $submitButtonLabel }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Optional: Place to the bottom of scripts -->
<script>
    const myModal = new bootstrap.Modal(
        document.getElementById("modalId"),
        options,
    );
</script>
