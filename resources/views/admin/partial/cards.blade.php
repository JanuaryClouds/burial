@props([
    'lastLogs' => null,
    'cardData' => [],
])
<div class="row g-5 g-xl-8">
    @foreach ($cardData as $statistic)
        <div class="col">
            <a href="{{ $statistic['link'] ?? '#' }}"
                class="card w-100 p-10 hover-elevate-up parent-hover overflow-hidden position-relative">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="parent-hover-primary">
                        <p class="fw-bold fs-1">{{ $statistic['count'] }}</p>
                        <p class="fs-5 mb-0">{{ $statistic['label'] }}</p>
                    </span>
                </div>
                <i class="ki-duotone {{ $statistic['icon'] }} position-absolute top-50 end-0 translate-middle-y opacity-25 parent-hover-primary"
                    style="z-index: 0; right: 10rem; font-size: 15rem; rotate: 15deg">
                    @for ($index = 0; $index < $statistic['pathsCount']; $index++)
                        <span class="path{{ $index + 1 }}"></span>
                    @endfor
                </i>
            </a>
        </div>
    @endforeach
</div>
