<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ sidebarMini: $persist(true), screenSmall: window.innerWidth < 1024 }" x-init="window.addEventListener('resize', () => { screenSmall = window.innerWidth < 1024; })"
    data-bs-theme="system" data-bs-theme-mode="system">

<head>
    @include('partials.document-head')
</head>

<body id="kt_body" class="app-blank"
    style="background: url('{{ asset('images/white_bg_city.png') }}') no-repeat center center / cover; overflow-x: hidden; background-attachment: fixed;">
    @include('partials.theme-script')
    <div class="d-flex flex-column flex-root min-vw-100" id="kt_app_root">
        <x-loader />
        @includeWhen(!Route::is('login'), 'client.partial.header')
        @yield('content')
        @include('components.footer')
    </div>
    @include('components.alert')
    @include('partials.document-scripts')
</body>

</html>
