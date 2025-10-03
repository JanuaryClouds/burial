@props(['statistics'])
<div class="card card-statistic-2">
    <div class="card-stats pb-4">
        <div class="card-stats-title">
            {{ Str::title(Str::replace('_', ' ', $statistics['type'])) }}
        </div>
        <div class="card-stats-items">
            @foreach ($statistics['numbers'] as $label => $number)
                <div class="card-stats-item">
                    <div class="card-stats-item-count">
                        {{ $number }}
                    </div>
                    <div class="card-stats--item-label">
                        {{ Str::title($label) }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>