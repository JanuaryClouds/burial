@php
    $isStaff = auth()->user()->hasRole('staff');
@endphp
<div class="row g-6 align-items-stretch">
    @unless ($isStaff)
        <div class="col col-md-6 col-lg-6 d-flex">
            @include('components.card-link', [
                'title' => 'Apply as a Client',
                'route' => route('general.intake.form'),
                'active' => !App\Models\SystemSetting::first()?->maintenance_mode,
                'icon' => 'ki-duotone ki-plus-square',
                'icon_paths' => 3,
                'description' => 'Apply as a client to be given a funeral assistance',
            ])
        </div>
    @endunless
    <div class="col col-md-6 col-lg-6 d-flex">
        @include('components.card-link', [
            'title' => $isStaff ? 'All Clients' : 'Client History',
            'route' => route('client.index'),
            'active' => $isStaff ? true : auth()->user()->clients()->count() > 0 || app()->hasDebugModeEnabled(),
            'icon' => 'ki-duotone ki-time',
            'icon_paths' => 2,
            'description' => $isStaff ? 'Check all clients' : 'Check your history as a client',
        ])
    </div>
    @if ($isStaff)
        <div class="col col-md-6 col-lg-6 d-flex">
            @include('components.card-link', [
                'title' => 'Referrals',
                'route' => route('referral.index'),
                'active' => \App\Models\Referral::count() > 0 || app()->hasDebugModeEnabled(),
                'icon' => 'ki-duotone ki-route',
                'icon_paths' => 4,
                'description' => 'Check all referrals',
            ])
        </div>
    @endif
    @unless ($isStaff)
        <div class="col col-md-6 col-lg-6 d-flex">
            @include('components.card-link', [
                'title' => 'Beneficiaries',
                'route' => route('beneficiary.index'),
                'active' => auth()->user()->clients()->count() > 0 || app()->hasDebugModeEnabled(),
                'icon' => 'ki-duotone ki-user-square',
                'icon_paths' => 3,
                'description' => 'Family members that you have applied in your applications',
            ])
        </div>
        <div class="col col-md-6 col-lg-6 d-flex">
            @include('components.card-link', [
                'title' => 'Appointment Interviews',
                'route' => route('interview.index'),
                'active' =>
                    auth()->user()->clients()->whereHas('interviews')->exists() || app()->hasDebugModeEnabled(),
                'active_link' => route('interview.index'),
                'icon' => 'ki-duotone ki-message-question',
                'icon_paths' => 3,
                'description' => 'Check your history of appointment interviews',
            ])
        </div>
    @endunless
    <div class="col col-md-6 col-lg-6 d-flex">
        @include('components.card-link', [
            'title' => 'Burial Assistances',
            'route' => route('burial.index'),
            'active' => $isStaff
                ? true
                : auth()->user()->clients()->whereHas('claimant')->exists() || app()->hasDebugModeEnabled(),
            'icon' => 'ki-duotone ki-file-up',
            'icon_paths' => 2,
            'description' => $isStaff ? 'Burial Assistances' : 'Burial Assistances you have requested before',
        ])
    </div>
    <div class="col col-md-6 col-lg-6 d-flex">
        @include('components.card-link', [
            'title' => 'Libreng Libing',
            'icon' => 'ki-duotone ki-file-up',
            'route' => route('funeral.index'),
            'active' => $isStaff
                ? true
                : auth()->user()->clients()->whereHas('funeralAssistance')->exists() ||
                    app()->hasDebugModeEnabled(),
            'icon_paths' => 2,
            'description' => $isStaff ? 'Libreng Libing' : 'Applications for Libreng Libing for your loved ones',
        ])
    </div>
</div>
