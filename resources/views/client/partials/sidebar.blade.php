@include('components.sidebar-link', [
    'route' => route('general.intake.form'),
    'active_link' => 'general.intake.form',
    'icon' => 'add-files',
    'icon_paths' => 3,
    'text' => 'Apply',
    'long_text' => 'Apply as a client',
])
@include('components.sidebar-link', [
    'route' => route('interview.index'),
    'active_link' => 'interview.index',
    'icon' => 'message-question',
    'icon_paths' => 3,
    'text' => 'Interviews',
])
