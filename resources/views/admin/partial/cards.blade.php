@props([
    'lastLogs' => null,
    'cardData' => [],
])
<div class="row g-5 g-xl-8">
    @foreach ($cardData as $statistic)
        <div class="col">
            <a href="{{ $statistic['link'] ?? null }}"
                class="card flex-column justify-content-start align-items-start text-start w-100 p-10 hover-elevate-up parent-hover">
                <i
                    class="ki-duotone {{ $statistic['icon'] }} fs-2tx mb-5 ms-n1 text-gray-500 parent-hover-primary parent-hover-rotate-start">
                    @for ($index = 0; $index < $statistic['pathsCount']; $index++)
                        <span class="path{{ $index + 1 }}"></span>
                    @endfor
                </i>
                <span class="fs-5 parent-hover-primary">{{ $statistic['count'] }} {{ $statistic['label'] }}</span>
            </a>
        </div>
    @endforeach
    @if (auth()->user())
        @if (count($cardData) < 4)
            @can('add-updates')
                @if ($lastLogs?->count() > 0)
                    <div class="col">
                        <a href="{{ route('burial-assistances.manage', ['id' => $lastLogs->last()->burialAssistance->id]) }}"
                            class="card bg-success flex-column justify-content-start align-items-start text-start w-100 p-10 hover-elevate-up">
                            <i class="ki-duotone ki-entrance-left fs-2tx mb-5 ms-n1 text-white">
                                <path class="path1"></path>
                                <path class="path2"></path>
                            </i>
                            <span class="fs-5 text-white">Continue Last Application</span>
                        </a>
                    </div>
                @endif
            @endcan
        @endif
    @endif
</div>
