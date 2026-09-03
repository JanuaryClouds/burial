@php
	$isStaff = auth()->user()->hasRole('staff');
@endphp
<div class="row g-6 align-items-stretch">
	@unless ($isStaff)
		<div class="col col-md-6 col-lg-6 d-flex">
			@include('components.card.link', [
				'title' => 'Apply as a Client',
				'route' => route('application.create'),
				'active' => !App\Models\SystemSetting::first()?->maintenance_mode,
				'icon' => 'ki-duotone ki-plus-square',
				'icon_paths' => 3,
				'description' => 'Apply as a client to be given a funeral assistance',
			])
		</div>
	@endunless
	<div class="col col-md-6 col-lg-6 d-flex">
		@include('components.card.link', [
			'title' => $isStaff ? 'All Clients' : 'Client History',
			'route' => route('client.index'),
			'active' => $isStaff ? true : auth()->user()->clients()->count() > 0 || app()->hasDebugModeEnabled(),
			'icon' => 'ki-duotone ki-time',
			'icon_paths' => 2,
			'description' => $isStaff ? 'Check all clients' : 'Check your history as a client',
		])
	</div>
	<div class="col col-md-6 col-lg-6 d-flex">
		@include('components.card.link', [
			'title' => 'Referrals',
			'route' => route('referral.index'),
			'active' => \App\Models\Referral::count() > 0 || app()->hasDebugModeEnabled(),
			'icon' => 'ki-duotone ki-route',
			'icon_paths' => 4,
			'description' => 'Check all referrals',
		])
	</div>
	<div class="col col-md-6 col-lg-6 d-flex">
		@include('components.card.link', [
			'title' => 'Beneficiaries',
			'route' => route('beneficiary.index'),
			'active' => auth()->user()->clients()->count() > 0 || app()->hasDebugModeEnabled(),
			'icon' => 'ki-duotone ki-user-square',
			'icon_paths' => 3,
			'description' => 'Family members that you have applied in your applications',
		])
	</div>
	<div class="col col-md-6 col-lg-6 d-flex">
		@include('components.card.link', [
			'title' => 'Appointment Interviews',
			'route' => route('interview.index'),
			'active' => auth()->user()->clients()->whereHas('interviews')->exists() || app()->hasDebugModeEnabled(),
			'active_link' => route('interview.index'),
			'icon' => 'ki-duotone ki-message-question',
			'icon_paths' => 3,
			'description' => 'Check your history of appointment interviews',
		])
	</div>
	<div class="col col-md-6 col-lg-6 d-flex">
		@include('components.card.link', [
			'title' => 'Applications',
			'icon' => 'ki-duotone ki-file-up',
			'route' => route('application.index'),
			'active' => $isStaff
				? true
				: auth()->user()->clients()->whereHas('application')->exists() || app()->hasDebugModeEnabled(),
			'icon_paths' => 2,
			'description' => $isStaff ? 'Applications' : 'Applications for burial assistance for your loved ones',
		])
	</div>
</div>
