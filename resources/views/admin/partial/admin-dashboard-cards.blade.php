@props(['cardData'])
<div class="row">
    @foreach ($cardData as $statistic)
        <div class="col-12 col-md-6 col-lg-3">
            <div class="card card-statistic-1">
                <div class="card-icon d-flex align-items-center justify-content-center {{ $statistic['bg'] }}">
                    <i class="fas {{ $statistic['icon'] }}"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-body">
                        <div class="card-header">
                            <h4>{{ $statistic['label'] }}</h4>
                        </div>
                        <p class="card-body">{{ $statistic['count'] }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>