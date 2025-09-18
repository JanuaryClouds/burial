<!DOCTYPE html>
<html 
    lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
    x-data="{ sidebarMini: $persist(true), screenSmall: window.innerWidth < 1024}" 
    x-init="window.addEventListener('resize', () => {screenSmall = window.innerWidth < 1024;})"
>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/4f2d7302b1.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast/dist/css/iziToast.min.css">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

	 <!-- Start GA -->
    <script async
        src="https://www.googletagmanager.com/gtag/js?id=UA-94034622-3"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }
        gtag('js', new Date());

        gtag('config', 'UA-94034622-3');
    </script>
    <!-- END GA -->
</head>
<body :class="(sidebarMini || screenSmall) ? 'sidebar-mini' : ''">
	<div id="app">
        @include('components.header')
        @include('components.sidebar')
		<div class="main-wrapper">
			@yield('content')
            <script src="https://cdn.jsdelivr.net/npm/izitoast/dist/js/iziToast.min.js"></script>
            <x-toast-notification />
		</div>
        @include('components.footer')
	</div>
</body>
</html>