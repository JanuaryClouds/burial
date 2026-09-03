<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
	data-bs-theme="system"
	data-bs-theme-mode="system"
	class="overflow-x-hidden">

<head>
	@include('partials.document.head')
</head>

<body id="kt_body"
	class="app-blank"
	style="background: url('{{ asset('images/white_bg_city.png') }}') no-repeat center center / cover; background-attachment: fixed; max-width: 100vw;">
	<div class="d-flex flex-column flex-root"
		id="kt_app_root">
		@include('partials.theme.script')
		<x-loader />
		@yield('content')
	</div>
	@include('components.notification.modal')
	@include('partials.document.scripts')
</body>

</html>
