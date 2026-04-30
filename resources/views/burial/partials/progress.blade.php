<div class="d-flex gap-6 justify-content-center align-items-center">
    <div class="d-flex gap-6 justify-content-center align-items-center">
        <p class="mb-0 text-nowrap">
            Step {{ $current_step . ' / ' . $totalSteps }}
        </p>
    </div>
    <div class="progress w-100">
        <div class="progress-bar" role="progressbar" style="width: {{ $progress }}%;"
            aria-valuenow="{{ $progress }}" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
    @php
        $badge_color = match ($data->status) {
            'pending' => 'warning',
            'processing' => 'primary',
            'approved' => 'success',
            'released' => 'success',
            'rejected' => 'danger',
            default => 'info',
        };

        $status = $data->status;
        if ($data->status == 'approved') {
            $status = 'For Pickup';
        }
    @endphp
    <span class="badge rounded-pill text-bg-{{ $badge_color }}">
        {{ Str::upper($status) }}
    </span>
</div>
