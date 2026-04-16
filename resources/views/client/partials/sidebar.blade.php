@include('components.sidebar-link', [
    'route' => route('history'),
    'active' => auth()->user()->clients()->count() > 0,
    'icon' => 'time',
    'icon_paths' => 2,
    'text' => 'History',
    'active_link' => 'history',
])
@include('components.sidebar-link', [
    'route' => route('interview.index'),
    'active' => auth()->user()->clients()->whereHas('interviews')->exists(),
    'icon' => 'message-question',
    'icon_paths' => 3,
    'text' => 'Interviews',
    'active_link' => 'interview.index',
])
