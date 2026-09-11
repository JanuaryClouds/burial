@include('components.sidebar.link', [
	'route' => route('dashboard'),
	'activeLink' => 'dashboard',
	'icon' => 'home-2',
	'iconPathsCount' => 2,
	'label' => 'Dashboard',
])
@unlessrole('staff')
	@include('components.sidebar.link', [
		'route' => route('client.create'),
		'activeLink' => 'client.create',
		'icon' => 'add-files',
		'iconPathsCount' => 3,
		'label' => 'Apply',
		'description' => 'Apply as Applicant',
	])
@endunlessrole
