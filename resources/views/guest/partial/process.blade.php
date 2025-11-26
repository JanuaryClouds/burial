<div class="d-flex flex-column gap-2">
    <h4 class="fs-1">Application Process</h4>

    <div class="d-flex flex-column gap-2">
        @foreach ($steps as $step)
            <div class="card parent-hover border-1">
                <div class="card-body d-flex align-items">
                    <span class="svg-icon fs-1">
                        {{ $step['order'] }}
                    </span>

                    <span class="ms-3 parent-hover-primary fs-4">
                        <p class="fw-bold">{{ $step['name'] }}</p>
                        <p class="text-muted">{{ $step['description'] }}</p>
                    </span>
                </div>
            </div>
            <div class="separator"></div>
        @endforeach
    </div>
</div>
