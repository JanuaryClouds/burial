@props(['user'])
@php
    use Spatie\Activitylog\Models\Activity;
    $lastLogin = Activity::where('causer_id', $user->id)
        ->where('description', 'Successful login attempt')
        ->orderBy('created_at', 'desc')
        ->first();
@endphp
<div class="card card-statistic-1">
<div class="card-icon d-flex align-items-center justify-content-center bg-secondary">
    <i class="fas fa-clock-rotate-left"></i>
</div>
<div class="card-wrap">
    <div class="card-body">
        <div class="card-header">
            <h4>Last Login</h4>
        </div>
        <div class="card-body mt-3">
            <p class="card-body">
                {{ $lastLogin ? $lastLogin->created_at->format('F j, Y h:i A') : 'Never' }}
            </p>
        </div>
    </div>
</div>
</div>