@include('components.sidebar-link', [
	'route' => route('dashboard'),
	'active_link' => 'dashboard',
	'icon' => 'home-2',
	'icon_paths' => 2,
	'text' => 'Dashboard',
])
@unlessrole('staff')
	@include('components.sidebar-link', [
		'route' => route('client.create'),
		'icon' => 'add-files',
		'icon_paths' => 3,
		'text' => 'Apply',
		'long_text' => 'Apply as Applicant',
	])
@endunlessrole
