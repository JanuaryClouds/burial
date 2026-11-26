@include('components.sidebar.link', [
	'route' => route('dashboard'), 'navigate' => true,
	'activeLink' => 'dashboard',
	'icon' => 'home-2',
	'iconPathsCount' => 2,
	'label' => 'Dashboard',
])
@if (! auth()->user()->isInternal())
	@include('components.sidebar.link', [
		'route' => route('client.create'), 'navigate' => true,
		'activeLink' => '',
		'icon' => 'add-files',
		'iconPathsCount' => 3,
		'label' => 'Apply',
		'description' => 'Apply as Applicant',
	])
@endif
