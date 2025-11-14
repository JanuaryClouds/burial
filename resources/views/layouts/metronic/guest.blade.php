<!DOCTYPE html>
<html 
    lang="{{ str_replace('_', '-', app()->getLocale()) }}" 
    x-data="{ sidebarMini: $persist(true), screenSmall: window.innerWidth < 1024}" 
    x-init="window.addEventListener('resize', () => {screenSmall = window.innerWidth < 1024;})"
>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <script src="https://kit.fontawesome.com/4f2d7302b1.js" crossorigin="anonymous"></script> -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast/dist/css/iziToast.min.css">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">

    <link rel="stylesheet" href="{{ asset('metronic/plugins/global/plugins.bundle.css') }}">
    <link rel="stylesheet" href="{{ asset('metronic/css/style.bundle.css') }}">
    @vite(['resources/js/metronic-app.js', 'public/metronic/css/style.bundle.css', 'public/metronic/plugins/global/plugins.bundle.css'])
</head>
<body id="kt_body" class="app-blank"
	style="background: url('{{ asset('images/white_bg_city.png') }}') no-repeat center center / cover; overflow-x: hidden; background-attachment: fixed;">
    <div 
        id="splashScreen"
        style="
            position: fixed; inset: 0; z-index: -1; display: flex; opacity: 0; align-items: center; justify-content: center; overflow: hidden;
            background: url('{{ asset('images/splash_screen.png') }}') no-repeat center center / cover;
        "
    >
    </div>

    <div class="d-flex flex-column flex-root" id="kt_app_root">
        @yield('content')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/izitoast/dist/js/iziToast.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    @include('components.alert')
    @include('components.toast-notification')
    <script src="{{ asset('metronic/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('metronic/js/scripts.bundle.js') }}"></script>
    @if (Request::is("burial-assistance"))
        <script src="{{ asset('metronic/js/custom/utilities/modals/create-account.js') }}"></script>
    @endif
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const element = document.querySelector('#kt_create_account_stepper');
            if (element) {
                new KTStepper(element); // Manually initialize
            }
        });
    </script>
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
                    link.target === '_blank' ? null : e.preventDefault();
                    const url = link.getAttribute('href');
                    if (
                        !url ||
                        url.startsWith('#') ||
                        url.startsWith('javascript:') ||
                        link.target === '_blank' ||
                        url.includes('://') && !url.includes(window.location.host)
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