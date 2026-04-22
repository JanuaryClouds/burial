<div wire:poll.10s="loadCount">
    <a href="{{ $route ?? '#' }}" class="card w-100 p-10 hover-elevate-up parent-hover overflow-hidden position-relative">
        <div class="d-flex justify-content-between align-items-center">
            <span class="parent-hover-primary">
                <p class="fw-bold fs-1">{{ $count ?? 0 }}</p>
                <p class="fs-5 mb-0">{{ $label ?? '' }}</p>
            </span>
        </div>
        <i class="ki-duotone ki-{{ $iconName ?? 'abstract' }} position-absolute top-50 end-0 translate-middle-y opacity-25 parent-hover-primary"
            style="z-index: 0; right: 10rem; font-size: 15rem; rotate: 15deg">
            @for ($index = 0; $index < ($iconPathsCount ?? 2); $index++)
                <span class="path{{ $index + 1 }}"></span>
            @endfor
        </i>
    </a>
</div>
