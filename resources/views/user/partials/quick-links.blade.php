<div class="row g-6 align-items-stretch">
    <div class="col-12 col-md-6 col-lg-6 d-flex">
        @include('components.card-link', [
            'title' => 'Apply as a Client',
            'route' => route('general.intake.form'),
            'active' => !App\Models\SystemSetting::first()?->maintenance_mode,
            'icon' => 'ki-duotone ki-plus-square',
            'icon_paths' => 3,
            'description' => 'Apply as a client to be given a funeral assistance',
        ])
    </div>
    <div class="col-12 col-md-6 col-lg-6 d-flex">
        @include('components.card-link', [
            'title' => 'Client History',
            'route' => route('client.index'),
            'active' => auth()->user()->clients()->count() > 0 || app()->hasDebugModeEnabled(),
            'icon' => 'ki-duotone ki-time',
            'icon_paths' => 2,
            'description' => 'Check your history as a client',
        ])
    </div>
    <div class="col-12 col-md-6 col-lg-6 d-flex">
        @include('components.card-link', [
            'title' => 'Beneficiaries',
            'route' => route('beneficiary.index'),
            'active' => auth()->user()->clients()->count() > 0 || app()->hasDebugModeEnabled(),
            'icon' => 'ki-duotone ki-user-square',
            'icon_paths' => 3,
            'description' => 'Family members that you have applied in your applications',
        ])
    </div>
    <div class="col-12 col-md-6 col-lg-6 d-flex">
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
    <div class="col-12 col-md-6 col-lg-6 d-flex">
        @include('components.card-link', [
            'title' => 'Burial Assistances',
            'route' => route('burial.index'),
            'active' => auth()->user()->clients()->whereHas('claimant')->exists() || app()->hasDebugModeEnabled(),
            'icon' => 'ki-duotone ki-file-up',
            'icon_paths' => 2,
            'description' => 'Burial Assistances you have requested before',
        ])
    </div>
    <div class="col-12 col-md-6 col-lg-6 d-flex">
        @include('components.card-link', [
            'title' => 'Libreng Libing',
            'icon' => 'ki-duotone ki-file-up',
            'route' => route('funeral.index'),
            'active' =>
                auth()->user()->clients()->whereHas('funeralAssistance')->exists() || app()->hasDebugModeEnabled(),
            'icon_paths' => 2,
            'description' => 'Applications for Libreng Libing for your loved ones',
        ])
    </div>
</div>
