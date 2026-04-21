<a href="#" class="btn btn-icon btn-custom btn-active-color-primary position-relative show menu-dropdown"
    data-kt-menu-trigger="click" data-kt-menu-attach="parent" data-kt-menu-placement="bottom-end" wire:poll.30s>
    <i class="ki-duotone ki-notification fs-1">
        <span class="path1"></span>
        <span class="path2"></span>
        <span class="path3"></span>
    </i>

    @if ($notifications)
        <span
            class="bullet bullet-dot bg-success h-15px w-15px position-absolute translate-middle top-0 start-100 animation-blink"></span>
    @endif
</a>
