<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
	data-bs-theme="system"
	data-bs-theme-mode="system"
	class="overflow-x-hidden">

<head>
    @if($recoveryPage ?? false)
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="referrer" content="no-referrer">
        <title>Password recovery</title>
        <link rel="stylesheet" href="{{ asset('metronic/css/style.bundle.css') }}">
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @else
        @include('partials.page.head')
    @endif
</head>

<body id="kt_body"
	class="app-blank"
	style="background: url('{{ asset('images/white_bg_city.png') }}') no-repeat center center / cover; background-attachment: fixed; max-width: 100vw;">
	<div class="d-flex flex-column flex-root"
		id="kt_app_root">
		@include('partials.theme.script')
        @unless($recoveryPage ?? false)<x-loader />@endunless
		@yield('content')
	</div>
    @unless($recoveryPage ?? false)
        @include('components.notification.alert')
        @include('partials.page.scripts')
    @endunless
    @if($recoveryPage ?? false)
        <script nonce="{{ $nonce ?? '' }}" src="{{ asset('js/immediate-mail.js') }}" defer></script>
        <script nonce="{{ $nonce ?? '' }}" src="{{ asset('js/password-visibility.js') }}" defer></script>
    @endif
</body>

</html>
