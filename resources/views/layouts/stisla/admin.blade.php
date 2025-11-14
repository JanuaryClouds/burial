<!DOCTYPE html>
<html 
    lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
    x-data="{ sidebarMini: $persist(true), screenSmall: window.innerWidth < 1400, loading: true}" 
    x-init="
        window.addEventListener('resize', () => { screenSmall = window.innerWidth < 1400 });
    "
>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/4f2d7302b1.js" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast/dist/css/iziToast.min.css">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body 
    :class="(sidebarMini || screenSmall) ? 'sidebar-mini' : ''"
>
    <div 
        id="splashScreen"
        style="
            position: fixed; inset: 0; z-index: -1; display: flex; opacity: 0; align-items: center; justify-content: center; overflow: hidden;
            background: url('{{ asset('images/splash_screen.png') }}') no-repeat center center / cover;
        "
    >
    </div>

	<div id="app" style="display: block;">
        @include('components.header')
        @include('components.sidebar')
		<div class="main-wrapper">
			@yield('content')
            <script src="https://cdn.jsdelivr.net/npm/izitoast/dist/js/iziToast.min.js"></script>
            <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
            <x-toast-notification />
            <x-alert />
		</div>
        @include('components.footer')
	</div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const splash = document.getElementById('splashScreen');
            
            function triggerLoading() {
                splash.style.zIndex = "9999";
                splash.style.opacity = "1";
                splash.style.transition = "opacity 0.3s ease";
                return;
            }

            document.querySelectorAll('form').forEach(defaultForm => {
                defaultForm.addEventListener("submit", function (e) {
                    e.preventDefault();
                    triggerLoading();
                    setTimeout(() => {
                        this.submit();
                    }, 1000)
                }) 
            });

            document.querySelectorAll('a[href]').forEach(link => {
                link.addEventListener("click", function (e) {
                    const url = link.getAttribute('href');
                    link.target === '_blank' || link.hasAttribute('data-no-loader') ? null : e.preventDefault();
                    if (
                        !url ||
                        url.startsWith('#') ||
                        url.startsWith('javascript:') ||
                        link.target === '_blank' ||
                        url.includes('://') && !url.includes(window.location.host) ||
                        link.hasAttribute('data-no-loader')
                    ) return;
                    triggerLoading();
                    
                    setTimeout(() => {
                        window.location.href = url;
                    }, 1000)
                });
            });
        });
    </script>
</body>
</html>