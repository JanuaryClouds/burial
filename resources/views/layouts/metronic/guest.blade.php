<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ sidebarMini: $persist(true), screenSmall: window.innerWidth < 1024 }" x-init="window.addEventListener('resize', () => { screenSmall = window.innerWidth < 1024; })"
    data-bs-theme="system" data-bs-theme-mode="system">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <script src="https://kit.fontawesome.com/4f2d7302b1.js" crossorigin="anonymous"></script> -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <title>{{ $page_title ?? 'CSWDO Burial Assistance' }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/izitoast/dist/css/iziToast.min.css">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/favicon.ico') }}">

    <link rel="stylesheet" href="{{ asset('metronic/plugins/global/plugins.bundle.css') }}">
    <link rel="stylesheet" href="{{ asset('metronic/css/style.bundle.css') }}">
    @vite(['resources/js/metronic-app.js', 'public/metronic/css/style.bundle.css', 'public/metronic/plugins/global/plugins.bundle.css', 'resources/css/custom.css'])
</head>

<body id="kt_body" class="app-blank"
    style="background: url('{{ asset('images/white_bg_city.png') }}') no-repeat center center / cover; overflow-x: hidden; background-attachment: fixed;">
    <script>
        var defaultThemeMode = "light";
        var themeMode;

        if (document.documentElement) {
            if (document.documentElement.hasAttribute("data-bs-theme-mode")) {
                themeMode = document.documentElement.getAttribute("data-bs-theme-mode");
            } else {
                if (localStorage.getItem("data-bs-theme") !== null) {
                    themeMode = localStorage.getItem("data-bs-theme");
                } else {
                    themeMode = defaultThemeMode;
                }
            }

            if (themeMode === "system") {
                themeMode = window.matchMedia("(prefers-color-scheme: dark)").matches ? "dark" : "light";
            }

            document.documentElement.setAttribute("data-bs-theme", themeMode);
        }
    </script>
    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <x-loader />
        @includeWhen(!Route::is('login.page'), 'client.partial.header')
        @yield('content')
        @include('components.footer')
    </div>
    <script src="https://cdn.jsdelivr.net/npm/izitoast/dist/js/iziToast.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    @include('components.alert')
    @include('components.toast-notification')
    <script src="{{ asset('metronic/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('metronic/js/scripts.bundle.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const element = document.querySelector('#kt_create_account_stepper');
            if (element) {
                new KTStepper(element); // Manually initialize
            }
        });
    </script>
</body>

</html>
